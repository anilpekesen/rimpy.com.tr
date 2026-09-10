<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class BreadcrumbWidget extends Widget
{
    public function key(): string { return 'breadcrumb'; }
    public function label(): string { return 'Yol izi'; }
    public function icon(): string { return 'chevrons-right'; }
    public function category(): string { return 'site'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'home_label' => Controls::text('Ana sayfa yazısı', ['default' => 'Ana Sayfa']),
                'separator' => Controls::select('Ayırıcı', [
                    'chevron' => '›', 'slash' => '/', 'dash' => '—', 'dot' => '·',
                ], ['default' => 'chevron']),
                'show_current' => Controls::toggle('Bulunulan sayfayı da göster', ['default' => true]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', Controls::$alignments, [
                    'selector' => '{{WRAPPER}} .crumbs', 'property' => 'justify-content',
                ]),
                'color' => Controls::color('Yazı rengi', ['selector' => '{{WRAPPER}} .crumb', 'default' => '@muted']),
                'active_color' => Controls::color('Bulunulan sayfa', ['selector' => '{{WRAPPER}} .crumb.is-current', 'default' => '@heading']),
                'typography' => Controls::typography(options: ['selector' => '{{WRAPPER}} .crumbs']),
            ]],
        ];
    }
}
