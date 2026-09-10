<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class HeadingWidget extends Widget
{
    public function key(): string { return 'heading'; }
    public function label(): string { return 'Başlık'; }
    public function icon(): string { return 'heading'; }
    public function category(): string { return 'basic'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'text' => Controls::textarea('Başlık metni', ['rows' => 2, 'default' => 'Buraya başlığınızı yazın']),
                'tag' => Controls::select('Önem sırası', [
                    'h1' => 'H1 — sayfanın ana başlığı',
                    'h2' => 'H2 — bölüm başlığı',
                    'h3' => 'H3 — alt başlık',
                    'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6',
                    'div' => 'Başlık değil, sadece görünüm',
                ], ['default' => 'h2', 'hint' => 'Arama motorları sayfayı bu sıraya göre okur. Her sayfada yalnızca bir H1 bulunmalı.']),
                'eyebrow' => Controls::text('Üst etiket', ['hint' => 'Başlığın üzerinde küçük punto ile çıkar.']),
                'link' => Controls::link('Bağlantı'),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', Controls::$alignments, [
                    'selector' => '{{WRAPPER}}', 'property' => 'text-align',
                ]),
                'color' => Controls::color('Yazı rengi', ['selector' => '{{WRAPPER}} .hd', 'default' => '@heading']),
                'typography' => Controls::typography(options: ['selector' => '{{WRAPPER}} .hd']),
                'eyebrow_color' => Controls::color('Üst etiket rengi', ['selector' => '{{WRAPPER}} .hd-eyebrow', 'default' => '@primary']),
                'max_width' => Controls::slider('En fazla genişlik', [
                    'max' => 1200, 'units' => ['px', '%'],
                    'selector' => '{{WRAPPER}} .hd', 'property' => 'max-width',
                ]),
            ]],
        ];
    }
}
