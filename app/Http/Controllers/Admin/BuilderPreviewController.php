<?php

namespace App\Http\Controllers\Admin;

use App\Builder\RenderContext;
use App\Builder\Renderer;
use App\Builder\StyleCompiler;
use App\Builder\Tree;
use App\Models\ContentItem;
use App\Models\Page;
use App\Models\Template;
use Illuminate\Support\Facades\Cache;

/**
 * Kurucunun ortasındaki önizleme çerçevesi.
 *
 * Tuval panelin içine gömülü çizildiğinde sitenin stilleri, yazı tipleri ve
 * betikleri yüklenemiyordu (panelin kendi stilleriyle çakışırlar); özel
 * widget'lar biçimsiz metin yığını gibi görünüyordu. Önizleme bu yüzden
 * ziyaretçinin gördüğü sayfa düzeniyle, ayrı bir çerçevede çizilir. Tablet ve
 * telefon görünümü de çerçeve daraldığı için gerçek kırılımlarla çalışır.
 *
 * Kaydedilmemiş ağaç, düzenleyici tarafından içeriğinden türetilmiş bir
 * anahtarla önbelleğe konur; geri almak aynı anahtara döner.
 */
class BuilderPreviewController
{
    public const TTL = 7200;

    public static function cacheKey(string $key): string
    {
        return 'builder-preview:'.$key;
    }

    public function __invoke(string $key)
    {
        $data = Cache::get(self::cacheKey($key));

        abort_unless(is_array($data) && ($data['user'] ?? null) === auth()->id(), 404);

        $subject = match ($data['type']) {
            'page' => Page::with('translations')->findOrFail($data['id']),
            'content' => ContentItem::with('translations', 'type')->findOrFail($data['id']),
            default => Template::findOrFail($data['id']),
        };

        app()->setLocale($data['locale']);

        $tree = Tree::fromArray(['elements' => $data['tree']]);

        return response()
            ->view('builder.preview', [
                'locale' => $data['locale'],
                'subject' => $subject,
                // Şablon (üst başlık, alt bilgi, bölüm) düzenlenirken sayfanın
                // kendi üst başlığı ve alt bilgisi çizilmez.
                'chrome' => $data['type'] !== 'template',
                'html' => app(Renderer::class)->render($tree, new RenderContext($data['locale'], editing: true, subject: $subject)),
                'css' => app(StyleCompiler::class)->compile($tree),
            ])
            ->header('Cache-Control', 'no-store');
    }
}
