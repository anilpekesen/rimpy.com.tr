<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class TextWidget extends Widget
{
    public function key(): string { return 'text'; }
    public function label(): string { return 'Metin'; }
    public function icon(): string { return 'text'; }
    public function category(): string { return 'basic'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'body' => Controls::rich('Metin', ['default' => '<p>Metninizi buraya yazın.</p>']),
                'columns' => Controls::slider('Sütun sayısı', [
                    'min' => 1, 'max' => 4, 'unit' => '',
                    'selector' => '{{WRAPPER}} .tx', 'property' => 'column-count',
                ]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', Controls::$alignments + [
                    'justify' => ['label' => 'İki yana', 'icon' => 'align-justify'],
                ], ['selector' => '{{WRAPPER}}', 'property' => 'text-align']),
                'color' => Controls::color('Yazı rengi', ['selector' => '{{WRAPPER}} .tx', 'default' => '@text']),
                'typography' => Controls::typography(options: ['selector' => '{{WRAPPER}} .tx']),
                'link_color' => Controls::color('Bağlantı rengi', ['selector' => '{{WRAPPER}} .tx a', 'default' => '@primary']),
            ]],
        ];
    }
}
