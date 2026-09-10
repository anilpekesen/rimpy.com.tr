<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class TabsWidget extends Widget
{
    public function key(): string { return 'tabs'; }
    public function label(): string { return 'Sekmeler'; }
    public function icon(): string { return 'panels-top-left'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Sekmeler', 'controls' => [
                'items' => Controls::repeater('Sekmeler', [
                    'title' => Controls::text('Sekme adı', ['default' => 'Sekme']),
                    'icon' => Controls::icon('İkon'),
                    'body' => Controls::rich('İçerik', ['default' => '<p>Sekme içeriği.</p>']),
                ], [
                    'title_field' => 'title',
                    'add_label' => 'Sekme ekle',
                    'default' => [
                        ['title' => 'Birinci', 'body' => '<p>Birinci sekmenin içeriği.</p>'],
                        ['title' => 'İkinci', 'body' => '<p>İkinci sekmenin içeriği.</p>'],
                    ],
                ]),
                'orientation' => Controls::select('Yerleşim', [
                    'horizontal' => 'Sekmeler üstte', 'vertical' => 'Sekmeler yanda',
                ], ['default' => 'horizontal']),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'tab_color' => Controls::color('Sekme yazısı', ['selector' => '{{WRAPPER}} .tab-btn', 'default' => '@muted']),
                'active_color' => Controls::color('Seçili sekme yazısı', ['selector' => '{{WRAPPER}} .tab-btn[aria-selected="true"]', 'default' => '@primary']),
                'active_bg' => Controls::color('Seçili sekme zemini', [
                    'selector' => '{{WRAPPER}} .tab-btn[aria-selected="true"]', 'property' => 'background-color',
                ]),
                'indicator' => Controls::color('Alt çizgi rengi', [
                    'selector' => '{{WRAPPER}} .tab-btn[aria-selected="true"]', 'property' => 'border-bottom-color', 'default' => '@primary',
                ]),
                'typography' => Controls::typography('Sekme yazısı', ['selector' => '{{WRAPPER}} .tab-btn']),
                'body_color' => Controls::color('İçerik rengi', ['selector' => '{{WRAPPER}} .tab-panel', 'default' => '@text']),
            ]],
        ];
    }
}
