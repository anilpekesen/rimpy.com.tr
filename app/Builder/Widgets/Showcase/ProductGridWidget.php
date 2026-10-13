<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Term;

/**
 * Ürün kartları: teknik özet ve "teklif sepetine ekle" düğmesiyle.
 * Öne çıkan ya da en yeni ürünleri çeker; üstte kategori süzgeci olabilir.
 */
class ProductGridWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'product_grid'; }
    public function label(): string { return 'Ürün kartları'; }
    public function icon(): string { return 'box'; }
    public function category(): string { return 'catalog'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                '02 — Öne çıkanlar', "Sepete ekle.\nFiyatı biz getirelim.",
                'Her proje farklı olduğu için fiyatlar teklifle verilir. Ürünleri sepete ekleyin, adetleri belirleyin; size özel teklifi 24 saat içinde hazırlayalım.'
            ) + [
                'source' => Controls::select('Hangi ürünler', [
                    'featured' => 'Öne çıkanlar', 'latest' => 'En yeniler',
                ], ['default' => 'featured']),
                'limit' => Controls::number('Ürün sayısı', ['default' => 8]),
                'filters' => Controls::toggle('Kategori süzgeci göster', ['default' => true]),
                'button_text' => Controls::text('Alt buton', ['default' => 'Tüm ürünleri gör']),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'tone' => Controls::select('Ton', ['light' => 'Açık zemin', 'dark' => 'Koyu zemin'], ['default' => 'light']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);
        $type = ContentType::with('taxonomies')->where('key', 'urunler')->first();

        $items = $type ? ContentItem::withTranslations()
            ->with('featuredImage', 'type', 'terms')
            ->where('content_type_id', $type->id)->live()
            // Öne çıkanlar önce gelir; sayıyı tamamlamaya yetmezlerse liste
            // diğer ürünlerle dolar, ızgarada yarım satır kalmaz.
            ->when(($settings['source'] ?? 'featured') === 'featured', fn ($q) => $q->orderByDesc('is_featured'))
            ->orderBy('sort')->orderByDesc('published_at')
            ->limit(max(1, (int) ($settings['limit'] ?? 8)))->get() : collect();

        $taxonomy = $type?->taxonomies->firstWhere('key', 'kategori');
        $filters = ($settings['filters'] ?? true) && $taxonomy
            ? Term::withTranslations()->where('taxonomy_id', $taxonomy->id)->whereNull('parent_id')->orderBy('sort')->get()
            : collect();

        $archive = $type ? \App\Support\Urls::prefix($context->locale).'/'.$type->prefix($context->locale) : '#';

        return compact('settings', 'items', 'filters', 'archive');
    }
}
