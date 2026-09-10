<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class LogoStripWidget extends Widget
{
    public function key(): string { return 'logo_strip'; }
    public function label(): string { return 'Logo şeridi'; }
    public function icon(): string { return 'layout-grid'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Logolar', 'controls' => [
                'logos' => Controls::gallery('Logolar'),
                'layout' => Controls::select('Yerleşim', [
                    'grid' => 'Sabit ızgara', 'marquee' => 'Sürekli kayan şerit',
                ], ['default' => 'grid']),
                'columns' => Controls::slider('Sütun sayısı', [
                    'min' => 2, 'max' => 8, 'unit' => '',
                    'default' => ['d' => ['size' => 5, 'unit' => ''], 't' => ['size' => 3, 'unit' => ''], 'm' => ['size' => 2, 'unit' => '']],
                    'condition' => ['layout' => 'grid'],
                ]),
                'speed' => Controls::slider('Kayma hızı', [
                    'min' => 10, 'max' => 90, 'unit' => 'sn', 'responsive' => false,
                    'default' => ['size' => 35, 'unit' => 'sn'],
                    'condition' => ['layout' => 'marquee'],
                ]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'grayscale' => Controls::toggle('Gri tonda göster, üzerine gelince renklendir', ['default' => true]),
                'height' => Controls::slider('Logo yüksekliği', [
                    'max' => 160, 'default' => ['d' => ['size' => 48, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .logo-item img', 'property' => 'height',
                ]),
                'gap' => Controls::slider('Aralık', [
                    'max' => 100, 'default' => ['d' => ['size' => 40, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .logo-strip', 'property' => 'gap',
                ]),
            ]],
        ];
    }
}
