<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class ButtonWidget extends Widget
{
    public function key(): string { return 'button'; }
    public function label(): string { return 'Buton'; }
    public function icon(): string { return 'mouse-pointer-click'; }
    public function category(): string { return 'basic'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'text' => Controls::text('Buton yazısı', ['default' => 'Bize ulaşın']),
                'link' => Controls::link('Bağlantı'),
                'icon' => Controls::icon('İkon'),
                'icon_position' => Controls::select('İkon konumu', [
                    'right' => 'Yazının sağında', 'left' => 'Yazının solunda',
                ], ['default' => 'right']),
                'full_width' => Controls::toggle('Alanı tamamen kaplasın'),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', Controls::$alignments, [
                    'selector' => '{{WRAPPER}}', 'property' => 'text-align',
                ]),
                'variant' => Controls::select('Biçim', [
                    'solid' => 'Dolu',
                    'outline' => 'Çerçeveli',
                    'ghost' => 'Sade',
                    'link' => 'Bağlantı gibi',
                ], ['default' => 'solid']),
                'size' => Controls::select('Boyut', [
                    'sm' => 'Küçük', 'md' => 'Orta', 'lg' => 'Büyük', 'xl' => 'Çok büyük',
                ], ['default' => 'md']),
                'bg' => Controls::color('Zemin rengi', [
                    'selector' => '{{WRAPPER}} .btn', 'property' => 'background-color', 'default' => '@primary',
                ]),
                'color' => Controls::color('Yazı rengi', ['selector' => '{{WRAPPER}} .btn', 'default' => '#ffffff']),
                'hover_bg' => Controls::color('Üzerine gelince zemin', [
                    'selector' => '{{WRAPPER}} .btn:hover', 'property' => 'background-color',
                ]),
                'hover_color' => Controls::color('Üzerine gelince yazı', ['selector' => '{{WRAPPER}} .btn:hover']),
                'radius' => Controls::dimensions('Köşe yuvarlaklığı', 'border-radius', ['selector' => '{{WRAPPER}} .btn']),
                'typography' => Controls::typography(options: ['selector' => '{{WRAPPER}} .btn']),
                'btn_padding' => Controls::dimensions('İç boşluk', 'padding', ['selector' => '{{WRAPPER}} .btn']),
            ]],
        ];
    }
}
