<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class CounterWidget extends Widget
{
    public function key(): string { return 'counter'; }
    public function label(): string { return 'Sayaç'; }
    public function icon(): string { return 'trending-up'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'value' => Controls::number('Sayı', ['default' => 250, 'translatable' => false]),
                'prefix' => Controls::text('Başına gelen', ['hint' => 'Örnek: ₺, +']),
                'suffix' => Controls::text('Sonuna gelen', ['hint' => 'Örnek: +, %, m²']),
                'title' => Controls::text('Açıklama', ['default' => 'Tamamlanan proje']),
                'duration' => Controls::slider('Sayma süresi', [
                    'min' => 0, 'max' => 5, 'step' => 0.5, 'unit' => 'sn', 'responsive' => false,
                    'default' => ['size' => 2, 'unit' => 'sn'],
                ]),
                'separator' => Controls::toggle('Binlik ayracı kullan', ['default' => true]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', Controls::$alignments, [
                    'selector' => '{{WRAPPER}} .counter', 'property' => 'text-align', 'default' => ['d' => 'center'],
                ]),
                'number_color' => Controls::color('Sayı rengi', ['selector' => '{{WRAPPER}} .counter-value', 'default' => '@primary']),
                'number_typography' => Controls::typography('Sayı yazısı', ['selector' => '{{WRAPPER}} .counter-value']),
                'title_color' => Controls::color('Açıklama rengi', ['selector' => '{{WRAPPER}} .counter-title', 'default' => '@muted']),
                'title_typography' => Controls::typography('Açıklama yazısı', ['selector' => '{{WRAPPER}} .counter-title']),
            ]],
        ];
    }
}
