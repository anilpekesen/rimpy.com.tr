<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Widget;

/** Büyük rakamlar şeridi; ekrana girince sayar. */
class StatBandWidget extends Widget
{
    public function key(): string { return 'stat_band'; }
    public function label(): string { return 'Rakam şeridi'; }
    public function icon(): string { return 'trending-up'; }
    public function category(): string { return 'showcase'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'items' => Controls::repeater('Rakamlar', [
                    'value' => Controls::number('Sayı', ['default' => 100]),
                    'suffix' => Controls::text('Ek (+, s, %)', ['default' => '+']),
                    'label' => Controls::text('Açıklama'),
                ], ['title_field' => 'label', 'default' => [
                    ['value' => 24, 'suffix' => 's', 'label' => 'İçinde yanıtlanan teklif talebi'],
                    ['value' => 700, 'suffix' => '', 'label' => 'Bar çalışma basıncı, tüm hidrolik hatta'],
                    ['value' => 12, 'suffix' => 'ay', 'label' => 'Garanti; Türkiye\'de servis ve yedek parça'],
                    ['value' => 22, 'suffix' => '+', 'label' => 'Stoktan teslim edilebilen model'],
                ]]),
            ]],
        ];
    }
}
