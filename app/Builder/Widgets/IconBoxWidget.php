<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class IconBoxWidget extends Widget
{
    public function key(): string { return 'icon_box'; }
    public function label(): string { return 'İkonlu kutu'; }
    public function icon(): string { return 'award'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'icon' => Controls::icon('İkon', ['default' => 'check-circle']),
                'image' => Controls::media('İkon yerine görsel'),
                'title' => Controls::text('Başlık', ['default' => 'Özellik başlığı']),
                'text' => Controls::textarea('Açıklama', ['default' => 'Bu özelliği kısaca anlatın.']),
                'link' => Controls::link('Bağlantı'),
                'layout' => Controls::select('Yerleşim', [
                    'top' => 'İkon üstte',
                    'left' => 'İkon solda',
                    'right' => 'İkon sağda',
                ], ['default' => 'top']),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', Controls::$alignments, [
                    'selector' => '{{WRAPPER}} .icon-box', 'property' => 'text-align',
                ]),
                'icon_color' => Controls::color('İkon rengi', ['selector' => '{{WRAPPER}} .ib-icon', 'default' => '@primary']),
                'icon_bg' => Controls::color('İkon zemini', [
                    'selector' => '{{WRAPPER}} .ib-icon', 'property' => 'background-color',
                ]),
                'icon_size' => Controls::slider('İkon boyutu', [
                    'max' => 120, 'selector' => '{{WRAPPER}} .ib-icon', 'property' => 'font-size',
                ]),
                'icon_radius' => Controls::dimensions('İkon köşeleri', 'border-radius', ['selector' => '{{WRAPPER}} .ib-icon']),
                'title_color' => Controls::color('Başlık rengi', ['selector' => '{{WRAPPER}} .ib-title', 'default' => '@heading']),
                'title_typography' => Controls::typography('Başlık yazısı', ['selector' => '{{WRAPPER}} .ib-title']),
                'text_color' => Controls::color('Metin rengi', ['selector' => '{{WRAPPER}} .ib-text', 'default' => '@muted']),
                'text_typography' => Controls::typography('Metin yazısı', ['selector' => '{{WRAPPER}} .ib-text']),
            ]],
        ];
    }
}
