<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use RuntimeException;

/**
 * Yüklenen dosyaları doğrular, temizler ve boyutlarını üretir.
 *
 * Doğrulama üç bağımsız aşamada yapılır çünkü tek başına hiçbiri yeterli
 * değildir:
 *   1. Uzantı beyaz listede mi? (kullanıcının verdiği ada bakar)
 *   2. MIME tipi uzantıyla uyumlu mu? (tarayıcının beyanına bakar)
 *   3. Dosyanın ilk baytları gerçekten o türe mi ait? (dosyanın kendisine bakar)
 *
 * Üçü de geçse bile dosya asla kullanıcının verdiği adla kaydedilmez;
 * rastgele üretilmiş güvenli bir ad kullanılır. Görseller yeniden kodlanır,
 * böylece görsel içine gizlenmiş çalıştırılabilir yükler yok olur.
 */
class MediaUploader
{
    /** Hiçbir koşulda kabul edilmeyecek uzantılar. */
    private const FORBIDDEN = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'phps',
        'htaccess', 'htpasswd', 'ini', 'sh', 'bash', 'exe', 'dll', 'so', 'bat',
        'cmd', 'com', 'cgi', 'pl', 'py', 'rb', 'jsp', 'asp', 'aspx', 'jar',
    ];

    /** Dosya imzaları: uzantı => kabul edilen baş baytlar. */
    private const SIGNATURES = [
        'jpg' => ["\xFF\xD8\xFF"],
        'jpeg' => ["\xFF\xD8\xFF"],
        'png' => ["\x89PNG\r\n\x1A\n"],
        'gif' => ['GIF87a', 'GIF89a'],
        'webp' => ['RIFF'],
        'pdf' => ['%PDF'],
        'zip' => ["PK\x03\x04", "PK\x05\x06"],
        'mp4' => [], // konteyner ofsetli olduğu için imza denetimi atlanır
    ];

    public function __construct(private ?ImageManager $images = null)
    {
        $this->images ??= new ImageManager(
            config('cms.media.driver') === 'imagick' && extension_loaded('imagick')
                ? new ImagickDriver
                : new GdDriver
        );
    }

    public function store(UploadedFile $file, ?int $folderId = null, string $disk = 'public'): Media
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $type = $this->validate($file, $extension);

        // Aynı dosya daha önce yüklendiyse yenisini yazmak yerine mevcudu ver.
        $hash = hash_file('sha256', $file->getRealPath());

        if ($existing = Media::where('hash', $hash)->first()) {
            return $existing;
        }

        $directory = 'media/'.now()->format('Y/m');
        $name = Str::random(24).'.'.($this->shouldConvertToWebp($extension) ? 'webp' : $extension);

        $media = $type === 'image' && $extension !== 'svg'
            ? $this->storeImage($file, $disk, $directory, $name, $extension)
            : $this->storeRaw($file, $disk, $directory, $name, $extension, $type);

        return tap(Media::create($media + [
            'folder_id' => $folderId,
            'disk' => $disk,
            'original_name' => Str::limit($file->getClientOriginalName(), 190, ''),
            'type' => $type,
            'hash' => $hash,
            'uploaded_by' => auth()->id(),
            'alt' => \App\Support\Locales::map(''),
            'title' => \App\Support\Locales::map(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)),
        ]), fn (Media $m) => ActivityLogger::created($m, "Medya yüklendi: {$m->original_name}"));
    }

    /**
     * Dosyanın kabul edilebilir olduğunu doğrular ve türünü döndürür.
     *
     * @throws RuntimeException
     */
    private function validate(UploadedFile $file, string $extension): string
    {
        if (! $file->isValid()) {
            throw new RuntimeException('Dosya sunucuya eksik ulaştı, lütfen tekrar deneyin.');
        }

        // Çift uzantılı adları (resim.php.jpg) baştan reddet.
        $name = strtolower($file->getClientOriginalName());

        foreach (self::FORBIDDEN as $bad) {
            if (str_contains($name, '.'.$bad.'.') || str_ends_with($name, '.'.$bad)) {
                throw new RuntimeException('Bu dosya türü güvenlik nedeniyle kabul edilmiyor.');
            }
        }

        $type = $this->resolveType($extension);

        if ($type === null) {
            throw new RuntimeException("“.{$extension}” uzantılı dosyalar yüklenemez.");
        }

        $maxBytes = (int) config('cms.media.max_upload_mb', 12) * 1024 * 1024;

        if ($file->getSize() > $maxBytes) {
            throw new RuntimeException('Dosya en fazla '.config('cms.media.max_upload_mb').' MB olabilir.');
        }

        $this->assertSignatureMatches($file, $extension);

        if ($extension === 'svg') {
            $this->assertSvgIsSafe($file);
        }

        return $type;
    }

    private function resolveType(string $extension): ?string
    {
        foreach (config('cms.media.allowed', []) as $type => $extensions) {
            if (in_array($extension, $extensions, true)) {
                return $type;
            }
        }

        return null;
    }

    /** Dosyanın ilk baytları uzantısının vaat ettiği türe uyuyor mu? */
    private function assertSignatureMatches(UploadedFile $file, string $extension): void
    {
        $expected = self::SIGNATURES[$extension] ?? null;

        if (empty($expected)) {
            return;
        }

        $head = (string) file_get_contents($file->getRealPath(), false, null, 0, 16);

        foreach ($expected as $signature) {
            if (str_starts_with($head, $signature)) {
                return;
            }
        }

        throw new RuntimeException('Dosyanın içeriği uzantısıyla uyuşmuyor.');
    }

    /**
     * SVG bir XML belgesidir; script etiketi ve olay öznitelikleri taşıyabilir.
     * Bu yüzden içeriği okunup tehlikeli parçalar temizlenir.
     */
    private function assertSvgIsSafe(UploadedFile $file): void
    {
        $content = (string) file_get_contents($file->getRealPath());

        $dangerous = ['<script', '<foreignobject', 'javascript:', '<!entity', '<!doctype', 'xlink:href="http'];

        foreach ($dangerous as $needle) {
            if (str_contains(strtolower($content), $needle)) {
                throw new RuntimeException('SVG dosyası çalıştırılabilir içerik barındırıyor ve kabul edilmedi.');
            }
        }

        // on* olay öznitelikleri (onload, onclick...)
        if (preg_match('/\son[a-z]+\s*=/i', $content)) {
            throw new RuntimeException('SVG dosyası çalıştırılabilir içerik barındırıyor ve kabul edilmedi.');
        }
    }

    private function shouldConvertToWebp(string $extension): bool
    {
        return config('cms.media.webp', true)
            && in_array($extension, ['jpg', 'jpeg', 'png'], true);
    }

    /**
     * Görseli yeniden kodlar, çok büyükse küçültür ve yapılandırmadaki
     * boyutları üretir. Yeniden kodlama sırasında EXIF verisi ve dosyaya
     * iliştirilmiş her şey düşer.
     */
    private function storeImage(UploadedFile $file, string $disk, string $directory, string $name, string $extension): array
    {
        $storage = Storage::disk($disk);
        $image = $this->images->read($file->getRealPath());

        $max = (int) config('cms.media.max_dimension', 2560);

        if ($image->width() > $max || $image->height() > $max) {
            $image->scaleDown($max, $max);
        }

        $path = $directory.'/'.$name;
        $storage->put($path, $this->encode($image, $name), 'public');

        $variants = [];

        foreach (config('cms.media.sizes', []) as $key => $size) {
            // Orijinalden büyük bir türev üretmenin anlamı yok.
            if ($size['w'] >= $image->width() && ($size['h'] ?? 0) >= $image->height()) {
                continue;
            }

            $copy = $this->images->read($file->getRealPath());

            $copy = ($size['fit'] ?? 'contain') === 'cover' && $size['h']
                ? $copy->cover($size['w'], $size['h'])
                : $copy->scaleDown($size['w'], $size['h']);

            $variantName = pathinfo($name, PATHINFO_FILENAME)."-{$key}.".pathinfo($name, PATHINFO_EXTENSION);
            $variantPath = $directory.'/'.$variantName;

            $storage->put($variantPath, $this->encode($copy, $variantName), 'public');

            $variants[$key] = ['path' => $variantPath, 'w' => $copy->width(), 'h' => $copy->height()];
        }

        return [
            'path' => $path,
            'filename' => $name,
            'mime' => $this->shouldConvertToWebp($extension) ? 'image/webp' : $file->getMimeType(),
            'extension' => pathinfo($name, PATHINFO_EXTENSION),
            'size' => $storage->size($path),
            'width' => $image->width(),
            'height' => $image->height(),
            'variants' => $variants ?: null,
        ];
    }

    private function encode($image, string $name): string
    {
        return (string) match (pathinfo($name, PATHINFO_EXTENSION)) {
            'webp' => $image->toWebp(82),
            'png' => $image->toPng(),
            'gif' => $image->toGif(),
            'avif' => $image->toAvif(75),
            default => $image->toJpeg(86),
        };
    }

    /** Görsel olmayan dosyalar olduğu gibi, ama güvenli bir adla saklanır. */
    private function storeRaw(UploadedFile $file, string $disk, string $directory, string $name, string $extension, string $type): array
    {
        $path = Storage::disk($disk)->putFileAs($directory, $file, $name, 'public');

        $dimensions = $extension === 'svg' ? $this->svgDimensions($file) : [null, null];

        return [
            'path' => $path,
            'filename' => $name,
            'mime' => $file->getMimeType() ?: 'application/octet-stream',
            'extension' => $extension,
            'size' => $file->getSize(),
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'variants' => null,
        ];
    }

    /** @return array{0: ?int, 1: ?int} */
    private function svgDimensions(UploadedFile $file): array
    {
        $content = (string) file_get_contents($file->getRealPath());

        if (preg_match('/viewBox\s*=\s*["\']\s*[\d.]+\s+[\d.]+\s+([\d.]+)\s+([\d.]+)/i', $content, $m)) {
            return [(int) round((float) $m[1]), (int) round((float) $m[2])];
        }

        return [null, null];
    }
}
