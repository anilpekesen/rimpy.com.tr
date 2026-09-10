<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Models\ContentItem;

/**
 * Sitedeki içerikleri (ürünler, haberler, projeler...) listeler.
 *
 * Kullanıcı hangi türü, hangi kategoriden, kaç tane ve nasıl sıralanmış
 * göstereceğini panelden seçer. Yeni bir ürün eklendiğinde sayfaya elle
 * dokunmak gerekmez; liste kendini günceller.
 */
class ContentListWidget extends Widget
{
    public function key(): string { return 'content_list'; }
    public function label(): string { return 'İçerik listesi'; }
    public function icon(): string { return 'layout-list'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Kaynak', 'controls' => [
                'query' => Controls::query('Ne gösterilsin?'),
                'layout' => Controls::select('Yerleşim', [
                    'grid' => 'Kart ızgarası',
                    'list' => 'Alt alta liste',
                    'carousel' => 'Kayan şerit',
                    'featured' => 'İlki büyük, diğerleri küçük',
                ], ['default' => 'grid']),
                'columns' => Controls::slider('Sütun sayısı', [
                    'min' => 1, 'max' => 5, 'unit' => '',
                    'default' => ['d' => ['size' => 3, 'unit' => ''], 't' => ['size' => 2, 'unit' => ''], 'm' => ['size' => 1, 'unit' => '']],
                ]),
                'show_image' => Controls::toggle('Görsel', ['default' => true]),
                'image_ratio' => Controls::select('Görsel oranı', [
                    '' => 'Kendi oranı', '1/1' => 'Kare', '4/3' => '4:3', '3/2' => '3:2', '16/9' => '16:9', '3/4' => 'Dikey',
                ], ['default' => '4/3', 'condition' => ['show_image' => true]]),
                'show_title' => Controls::toggle('Başlık', ['default' => true]),
                'show_excerpt' => Controls::toggle('Kısa açıklama', ['default' => true]),
                'excerpt_length' => Controls::number('Açıklama uzunluğu (kelime)', ['default' => 20, 'translatable' => false]),
                'show_date' => Controls::toggle('Tarih'),
                'show_category' => Controls::toggle('Kategori'),
                'show_button' => Controls::toggle('Devamını oku butonu'),
                'button_text' => Controls::text('Buton yazısı', ['default' => 'Devamını oku', 'condition' => ['show_button' => true]]),
                'pagination' => Controls::toggle('Sayfalama', [
                    'hint' => 'Kapalıyken yalnızca seçtiğiniz sayıda kayıt gösterilir.',
                ]),
                'empty_text' => Controls::text('Kayıt yoksa gösterilecek yazı', ['default' => 'Henüz içerik eklenmedi.']),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'gap' => Controls::slider('Kart aralığı', [
                    'max' => 80, 'default' => ['d' => ['size' => 24, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .cl', 'property' => 'gap',
                ]),
                'card_bg' => Controls::background('Kart zemini', ['selector' => '{{WRAPPER}} .cl-card']),
                'card_radius' => Controls::dimensions('Kart köşeleri', 'border-radius', ['selector' => '{{WRAPPER}} .cl-card']),
                'card_padding' => Controls::dimensions('Kart iç boşluğu', 'padding', ['selector' => '{{WRAPPER}} .cl-body']),
                'card_border' => Controls::border('Kart kenarlığı', ['selector' => '{{WRAPPER}} .cl-card']),
                'card_shadow' => Controls::shadow('Kart gölgesi', ['selector' => '{{WRAPPER}} .cl-card']),
                'title_color' => Controls::color('Başlık rengi', ['selector' => '{{WRAPPER}} .cl-title', 'default' => '@heading']),
                'title_typography' => Controls::typography('Başlık yazısı', ['selector' => '{{WRAPPER}} .cl-title']),
                'excerpt_color' => Controls::color('Açıklama rengi', ['selector' => '{{WRAPPER}} .cl-excerpt', 'default' => '@muted']),
                'hover' => Controls::select('Üzerine gelince', [
                    '' => 'Bir şey olmasın', 'lift' => 'Kart yükselsin', 'zoom' => 'Görsel yakınlaşsın', 'border' => 'Kenarlık renklensin',
                ], ['default' => 'lift']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        return [
            'settings' => $settings,
            'items' => $this->query($settings['query'] ?? [], $context),
        ];
    }

    private function query(array $query, RenderContext $context)
    {
        $typeKey = $query['type'] ?? null;

        if (! $typeKey) {
            return collect();
        }

        $builder = ContentItem::query()
            ->ofType($typeKey)
            ->live()
            ->with(['translations', 'featuredImage', 'type', 'terms.translations']);

        // Kategori süzgeci
        if (! empty($query['terms'])) {
            $terms = (array) $query['terms'];
            $builder->whereHas('terms', fn ($q) => $q->whereIn('terms.id', $terms));
        }

        if (! empty($query['featured_only'])) {
            $builder->where('is_featured', true);
        }

        // Bulunulan içeriğin kendisini listeden çıkar ("benzer ürünler" için).
        if (! empty($query['exclude_current']) && $context->subject instanceof ContentItem) {
            $builder->whereKeyNot($context->subject->getKey());
        }

        match ($query['order'] ?? 'recent') {
            'oldest' => $builder->orderBy('published_at'),
            'manual' => $builder->orderBy('sort')->orderByDesc('published_at'),
            'title' => $builder->orderBy('id'),
            'random' => $builder->inRandomOrder(),
            'popular' => $builder->orderByDesc('view_count'),
            default => $builder->orderByDesc('published_at'),
        };

        $limit = max(1, min(48, (int) ($query['limit'] ?? 6)));

        return ! empty($query['paginate'])
            ? $builder->paginate($limit)->withQueryString()
            : $builder->limit($limit)->get();
    }
}
