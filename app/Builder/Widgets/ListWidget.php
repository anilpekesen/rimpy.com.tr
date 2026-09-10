<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class ListWidget extends Widget
{
    public function key(): string { return 'list'; }
    public function label(): string { return 'Liste'; }
    public function icon(): string { return 'list-checks'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Maddeler', 'controls' => [
                'items' => Controls::repeater('Maddeler', [
                    'text' => Controls::text('Metin', ['default' => 'Liste maddesi']),
                    'icon' => Controls::icon('İkon', ['default' => 'check']),
                    'link' => Controls::link('Bağlantı'),
                ], [
                    'title_field' => 'text',
                    'add_label' => 'Madde ekle',
                    'default' => [
                        ['text' => 'Birinci madde', 'icon' => 'check'],
                        ['text' => 'İkinci madde', 'icon' => 'check'],
                        ['text' => 'Üçüncü madde', 'icon' => 'check'],
                    ],
                ]),
                'columns' => Controls::slider('Sütun sayısı', [
                    'min' => 1, 'max' => 4, 'unit' => '', 'default' => ['d' => ['size' => 1, 'unit' => '']],
                    'selector' => '{{WRAPPER}} .list', 'property' => 'column-count',
                ]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'icon_color' => Controls::color('İkon rengi', ['selector' => '{{WRAPPER}} .list-icon', 'default' => '@primary']),
                'text_color' => Controls::color('Yazı rengi', ['selector' => '{{WRAPPER}} .list-text', 'default' => '@text']),
                'typography' => Controls::typography(options: ['selector' => '{{WRAPPER}} .list-text']),
                'gap' => Controls::slider('Satır aralığı', [
                    'max' => 48, 'selector' => '{{WRAPPER}} .list', 'property' => 'gap',
                ]),
                'divider' => Controls::toggle('Satırları çizgiyle ayır'),
            ]],
        ];
    }
}
