<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Models\Menu;

class MenuWidget extends Widget
{
    public function key(): string { return 'menu'; }
    public function label(): string { return 'Menü'; }
    public function icon(): string { return 'menu'; }
    public function category(): string { return 'site'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Menü', 'controls' => [
                'menu_key' => ['type' => 'menu_select', 'label' => 'Hangi menü?'],
                'orientation' => Controls::select('Yerleşim', [
                    'horizontal' => 'Yatay', 'vertical' => 'Dikey',
                ], ['default' => 'horizontal']),
                'mobile' => Controls::select('Mobilde', [
                    'drawer' => 'Yandan açılan panel',
                    'dropdown' => 'Aşağı açılan liste',
                    'same' => 'Aynı şekilde göster',
                ], ['default' => 'drawer']),
                'breakpoint' => Controls::select('Ne zaman mobil görünüme geçsin?', [
                    'tablet' => 'Tablet ve altında', 'mobile' => 'Yalnızca telefonda',
                ], ['default' => 'tablet']),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', [
                    'flex-start' => ['label' => 'Sola', 'icon' => 'align-left'],
                    'center' => ['label' => 'Ortaya', 'icon' => 'align-center'],
                    'flex-end' => ['label' => 'Sağa', 'icon' => 'align-right'],
                ], ['selector' => '{{WRAPPER}} .nav', 'property' => 'justify-content']),
                'color' => Controls::color('Bağlantı rengi', ['selector' => '{{WRAPPER}} .nav-link', 'default' => '@heading']),
                'hover_color' => Controls::color('Üzerine gelince', ['selector' => '{{WRAPPER}} .nav-link:hover', 'default' => '@primary']),
                'active_color' => Controls::color('Bulunulan sayfa', ['selector' => '{{WRAPPER}} .nav-link.is-active', 'default' => '@primary']),
                'typography' => Controls::typography(options: ['selector' => '{{WRAPPER}} .nav-link']),
                'gap' => Controls::slider('Aralık', [
                    'max' => 60, 'default' => ['d' => ['size' => 28, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .nav', 'property' => 'gap',
                ]),
                'submenu_bg' => Controls::color('Alt menü zemini', [
                    'selector' => '{{WRAPPER}} .nav-sub', 'property' => 'background-color', 'default' => '#ffffff',
                ]),
                'indicator' => Controls::select('Bulunulan sayfa göstergesi', [
                    '' => 'Yok', 'underline' => 'Alt çizgi', 'pill' => 'Yuvarlak zemin',
                ], ['default' => 'underline']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        $menu = Menu::with('tree')->where('key', $settings['menu_key'] ?? 'header')->first();

        return ['settings' => $settings, 'menu' => $menu];
    }
}
