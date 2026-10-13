<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Support\ProductCatalog;

/**
 * Tork hesaplayıcı: cıvata çapı, kalite, yağlama ve ön yük oranından gerekli
 * torku hesaplar; o torku verebilen anahtarları ve gereken pompa basıncını
 * listeler. Hesap tarayıcıda yapılır; ürün verisi sayfaya gömülür.
 */
class TorqueCalculatorWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'torque_calculator'; }
    public function label(): string { return 'Tork hesaplayıcı'; }
    public function icon(): string { return 'gauge'; }
    public function category(): string { return 'catalog'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                'Mühendislik araçları', "Tork\nhesaplayıcı",
                'Cıvata ölçüsünü ve kalitesini seçin; gerekli torku, uygun anahtarı ve pompa basıncını hemen görün.'
            ) + [
                'category' => Controls::text('Anahtar kategorisi (adres)', ['default' => 'tork-aletleri']),
                'socket_category' => Controls::text('Lokma kategorisi (adres)', ['default' => 'darbe-lokmalari']),
                'headroom' => Controls::number('Kapasite payı (%)', ['default' => 90, 'hint' => 'Anahtar, maksimum torkunun bu oranına kadar önerilir.']),
                'note' => Controls::textarea('Alt not', ['default' => 'Basit yöntem T = K·F·d yaklaşımıdır; VDI 2230 yöntemi diş ve somun altı sürtünmesini ayrı ele alır ve sıkarken oluşan burulmayı gerilmeye katar. Akma değerleri ISO 898-1, ASTM A193/A320 ve SAE J429\'dan alınmıştır. Kritik bağlantılarda cıvata üreticisinin ve proje şartnamesinin değerlerini esas alın.']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        $wrenches = ProductCatalog::inCategory($settings['category'] ?? 'tork-aletleri', $context->locale)
            ->filter(fn ($p) => $p['tork_maks_nm'])->values();
        $sockets = ProductCatalog::inCategory($settings['socket_category'] ?? 'darbe-lokmalari', $context->locale)
            ->filter(fn ($p) => $p['hex'])->values();

        return compact('settings', 'wrenches', 'sockets');
    }
}
