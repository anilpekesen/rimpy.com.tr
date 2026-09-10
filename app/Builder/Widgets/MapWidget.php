<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;

class MapWidget extends Widget
{
    public function key(): string { return 'map'; }
    public function label(): string { return 'Harita'; }
    public function icon(): string { return 'map-pin'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Konum', 'controls' => [
                'address' => Controls::textarea('Adres', [
                    'rows' => 2,
                    'default' => '',
                    'hint' => 'Açık adresi yazın; harita otomatik olarak o noktayı gösterir.',
                ]),
                'zoom' => Controls::slider('Yakınlaştırma', [
                    'min' => 1, 'max' => 20, 'unit' => '', 'responsive' => false,
                    'default' => ['size' => 15, 'unit' => ''],
                ]),
                'height' => Controls::slider('Yükseklik', [
                    'max' => 900, 'default' => ['d' => ['size' => 420, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .map', 'property' => 'height',
                ]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'radius' => Controls::dimensions('Köşe yuvarlaklığı', 'border-radius', ['selector' => '{{WRAPPER}} .map']),
                'grayscale' => Controls::toggle('Gri tonda göster'),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);
        $address = trim((string) ($settings['address'] ?? ''));

        // Anahtar gerektirmeyen gömme adresi; müşteriden API anahtarı istemeyiz.
        $settings['embed'] = $address === '' ? null : 'https://maps.google.com/maps?'.http_build_query([
            'q' => $address,
            'z' => data_get($settings, 'zoom.size', 15),
            'output' => 'embed',
        ]);

        return ['settings' => $settings];
    }
}
