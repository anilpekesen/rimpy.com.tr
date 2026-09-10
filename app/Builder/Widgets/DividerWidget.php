<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class DividerWidget extends Widget
{
    public function key(): string { return 'divider'; }
    public function label(): string { return 'Ayırıcı çizgi'; }
    public function icon(): string { return 'minus'; }
    public function category(): string { return 'layout'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Çizgi', 'controls' => [
                'style' => Controls::select('Biçim', [
                    'solid' => 'Düz', 'dashed' => 'Kesikli', 'dotted' => 'Noktalı', 'double' => 'Çift',
                ], ['default' => 'solid', 'selector' => '{{WRAPPER}} .divider', 'property' => 'border-top-style']),
                'color' => Controls::color('Renk', [
                    'selector' => '{{WRAPPER}} .divider', 'property' => 'border-top-color', 'default' => '@border',
                ]),
                'thickness' => Controls::slider('Kalınlık', [
                    'max' => 20, 'selector' => '{{WRAPPER}} .divider', 'property' => 'border-top-width',
                ]),
                'width' => Controls::slider('Genişlik', [
                    'max' => 100, 'unit' => '%', 'units' => ['%', 'px'],
                    'selector' => '{{WRAPPER}} .divider', 'property' => 'width',
                ]),
                'align' => Controls::choose('Hizalama', Controls::$alignments, [
                    'selector' => '{{WRAPPER}}', 'property' => 'text-align',
                ]),
            ]],
        ];
    }
}
