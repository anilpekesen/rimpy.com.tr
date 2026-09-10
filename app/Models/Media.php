<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'folder_id', 'disk', 'path', 'filename', 'original_name', 'type', 'mime',
        'extension', 'size', 'width', 'height', 'hash', 'variants', 'alt', 'title',
        'description', 'uploaded_by',
    ];

    protected function casts(): array
    {
        return ['variants' => 'array', 'alt' => 'array', 'title' => 'array'];
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'folder_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    /**
     * İstenen boyutun herkese açık adresi. Boyut üretilmemişse (SVG, PDF ya da
     * zaten küçük bir görsel) orijinale düşer; böylece şablonlar hiçbir zaman
     * kırık bağlantı basmaz.
     */
    public function url(string $size = 'original'): string
    {
        $path = $size === 'original'
            ? $this->path
            : ($this->variants[$size]['path'] ?? $this->path);

        return Storage::disk($this->disk)->url($path);
    }

    /** Tarayıcının uygun boyutu seçebilmesi için srcset dizesi. */
    public function srcset(): ?string
    {
        if (! $this->isImage() || blank($this->variants)) {
            return null;
        }

        $parts = [];

        foreach ($this->variants as $variant) {
            if (! empty($variant['path']) && ! empty($variant['w'])) {
                $parts[$variant['w']] = Storage::disk($this->disk)->url($variant['path']).' '.$variant['w'].'w';
            }
        }

        if ($this->width) {
            $parts[$this->width] = $this->url().' '.$this->width.'w';
        }

        ksort($parts);

        return $parts ? implode(', ', $parts) : null;
    }

    public function altText(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return $this->alt[$locale] ?? $this->alt[\App\Support\Locales::default()] ?? '';
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
        $i = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), $i === 0 ? 0 : 1).' '.$units[$i];
    }

    /** Kayıt silinirken orijinal ve tüm türevleri diskten de temizlenir. */
    protected static function booted(): void
    {
        static::deleting(function (self $media) {
            $disk = Storage::disk($media->disk);
            $disk->delete($media->path);

            foreach ($media->variants ?? [] as $variant) {
                if (! empty($variant['path'])) {
                    $disk->delete($variant['path']);
                }
            }
        });
    }
}
