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
 * Ürünlerin ana kategorileri, büyük kartlar halinde. Kategoriler panelde
 * eklendikçe kartlar kendiliğinden çoğalır; görsel kategori görselinden gelir.
 */
class CategoryTilesWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'category_tiles'; }
    public function label(): string { return 'Kategori kartları'; }
    public function icon(): string { return 'blocks'; }
    public function category(): string { return 'catalog'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                '01 — Ürün grupları', "Her cıvata işi.\nTek tedarikçi.",
                'Tork aletlerinden 700 bar hidrolik sistemlere, saha ekibinizin ihtiyaç duyduğu her şey tek teklifte.'
            ) + [
                'content_type' => Controls::text('İçerik türü anahtarı', ['default' => 'urunler']),
                'children' => Controls::number('Kartta gösterilecek alt kategori sayısı', ['default' => 5]),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);
        $type = ContentType::with('taxonomies')->where('key', $settings['content_type'] ?? 'urunler')->first();
        $taxonomy = $type?->taxonomies->firstWhere('key', 'kategori');

        $terms = $taxonomy ? Term::withTranslations()
            ->with(['taxonomy', 'children' => fn ($q) => $q->where('is_active', true)->with('translations')])
            ->withCount('items')
            ->where('taxonomy_id', $taxonomy->id)->whereNull('parent_id')->where('is_active', true)
            ->orderBy('sort')->get() : collect();

        // Kategorinin kendi görseli yoksa altındaki ürünlerden birinin
        // fotoğrafı kullanılır (öne çıkan önce); kart hiçbir zaman boş kalmaz.
        $fallbacks = [];

        foreach ($terms as $term) {
            if ($term->image_id) {
                continue;
            }

            $ids = $term->children->pluck('id')->push($term->id);
            $fallbacks[$term->id] = ContentItem::where('content_type_id', $type->id)
                ->whereNotNull('featured_image_id')
                ->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $ids))
                ->live()
                ->orderByDesc('is_featured')->orderBy('sort')
                ->value('featured_image_id');
        }

        return compact('settings', 'terms', 'type', 'fallbacks');
    }
}
