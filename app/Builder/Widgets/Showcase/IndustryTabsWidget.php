<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;

/** Sektörler: solda liste, sağda seçilen sektörün önerilen seti. */
class IndustryTabsWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'industry_tabs'; }
    public function label(): string { return 'Sektör sekmeleri'; }
    public function icon(): string { return 'factory'; }
    public function category(): string { return 'showcase'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls('05 — Sektörler', "Nerede sıkı\nbağlantı varsa.") + [
                'items' => Controls::repeater('Sektörler', [
                    'title' => Controls::text('Sektör', ['default' => 'Sektör']),
                    'text' => Controls::textarea('Açıklama'),
                    'points' => Controls::textarea('Önerilen set (her satıra bir kalem)'),
                    'image' => Controls::media('Görsel'),
                    'link' => Controls::link('Bağlantı'),
                ], ['title_field' => 'title', 'default' => [
                    ['title' => 'Rüzgâr enerjisi', 'text' => 'Kule, kanat göbeği ve nasel cıvatalarında yüksek tekrar sayısı ve düşük operatör yorgunluğu.', 'points' => "Akülü tork anahtarı\nCıvata gerdirme seti\nKule flanşı lokmaları\nKalibrasyon sertifikası"],
                    ['title' => 'Petrokimya ve rafineri', 'text' => 'Boru hattı flanşlarında sızdırmazlık için çapraz sıkma, flanş ayırma ve hidrostatik test.', 'points' => "Düşük profilli tork anahtarı\nFlanş ayırıcı kama\nFlanş hizalama aleti\nHidrostatik test pompası"],
                    ['title' => 'İnşaat ve köprü', 'text' => 'Senkron kaldırma, mesnet değişimi ve ağır yük konumlandırma.', 'points' => "Çift etkili silindirler\nElektrikli pompa\nManifold ve manometre\nSenkron kaldırma hizmeti"],
                    ['title' => 'Madencilik', 'text' => 'Kırıcı, değirmen ve iş makinelerinde sahada hızlı müdahale.', 'points' => "Somun kesici\nHidrolik çektirme\nAğır hizmet lokmaları\nŞişe kriko"],
                    ['title' => 'Gemi ve liman', 'text' => 'Şaft kaplini, pervane ve vinç bağlantılarında kontrollü tork.', 'points' => "Kare tahrikli tork anahtarı\nTork çarpanı\nEl pompası\nYerinde sıkma hizmeti"],
                ]]),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        $settings['items'] = array_map(fn ($item) => [
            'image' => $context->media($item['image'] ?? null),
            'href' => $context->link($item['link'] ?? null),
            'list' => array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) ($item['points'] ?? ''))))),
        ] + $item, $settings['items'] ?? []);

        return compact('settings');
    }
}
