<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Support\ProductCatalog;

/**
 * Silindir seçici ve sistem kurucu: yük, kaldırma noktası, emniyet katsayısı
 * ve stroktan silindiri seçer; yağ hacmine göre pompayı, nokta sayısına göre
 * hortum ve manometreyi ekler. Önerilen set tek tıkla teklif sepetine girer.
 */
class CylinderSelectorWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'cylinder_selector'; }
    public function label(): string { return 'Silindir seçici'; }
    public function icon(): string { return 'box'; }
    public function category(): string { return 'catalog'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                'Mühendislik araçları', "Silindir seçici\nve sistem kurucu",
                'Yükü, kaldırma noktası sayısını ve gereken stroku girin; silindir, pompa, hortum ve manometre setini birlikte önerelim.'
            ) + [
                'cylinder_category' => Controls::text('Silindir kategorisi (adres)', ['default' => 'hidrolik-silindirler']),
                'pump_category' => Controls::text('Pompa kategorisi (adres)', ['default' => 'hidrolik-pompalar']),
                'hose_code' => Controls::text('Hortum model kodu', ['default' => 'RHC-7206']),
                'gauge_code' => Controls::text('Manometre model kodu', ['default' => 'RGA-63']),
                'note' => Controls::textarea('Alt not', ['default' => 'Öneri, silindirlerin yükü eşit paylaştığı varsayımıyla yapılır. Eksantrik yük, yan kuvvet ve senkron kaldırma gereken işlerde mühendisimizle görüşün.']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);
        $locale = $context->locale;

        $cylinders = ProductCatalog::inCategory($settings['cylinder_category'] ?? 'hidrolik-silindirler', $locale)
            ->filter(fn ($p) => $p['kapasite_ton'] && $p['strok_mm'])->values();
        $pumps = ProductCatalog::inCategory($settings['pump_category'] ?? 'hidrolik-pompalar', $locale)
            ->filter(fn ($p) => $p['kullanilabilir_yag_cm3'])->values();

        $accessories = ProductCatalog::inCategory('yuksek-basinc-hidrolik', $locale)->keyBy('code');
        $hose = $accessories->get($settings['hose_code'] ?? 'RHC-7206');
        $gauge = $accessories->get($settings['gauge_code'] ?? 'RGA-63');

        return compact('settings', 'cylinders', 'pumps', 'hose', 'gauge');
    }
}
