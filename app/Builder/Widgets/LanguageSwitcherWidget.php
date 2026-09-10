<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class LanguageSwitcherWidget extends Widget
{
    public function key(): string { return 'language_switcher'; }
    public function label(): string { return 'Dil seçici'; }
    public function icon(): string { return 'languages'; }
    public function category(): string { return 'site'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Görünüm', 'controls' => [
                'display' => Controls::select('Nasıl gösterilsin?', [
                    'code' => 'Kısaltma (TR / EN)',
                    'name' => 'Dil adı (Türkçe / English)',
                    'flag' => 'Bayrak',
                    'flag_code' => 'Bayrak ve kısaltma',
                ], ['default' => 'code']),
                'style' => Controls::select('Biçim', [
                    'inline' => 'Yan yana', 'dropdown' => 'Açılır liste',
                ], ['default' => 'inline']),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'color' => Controls::color('Yazı rengi', ['selector' => '{{WRAPPER}} .lang-link', 'default' => '@muted']),
                'active_color' => Controls::color('Seçili dil', ['selector' => '{{WRAPPER}} .lang-link.is-active', 'default' => '@heading']),
                'typography' => Controls::typography(options: ['selector' => '{{WRAPPER}} .lang-link']),
            ]],
        ];
    }
}
