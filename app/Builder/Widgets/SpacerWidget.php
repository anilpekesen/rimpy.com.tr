<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class SpacerWidget extends Widget
{
    public function key(): string { return 'spacer'; }
    public function label(): string { return 'Boşluk'; }
    public function icon(): string { return 'move-vertical'; }
    public function category(): string { return 'layout'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Boşluk', 'controls' => [
                'height' => Controls::slider('Yükseklik', [
                    'max' => 400, 'default' => ['d' => ['size' => 48, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .spacer', 'property' => 'height',
                ]),
            ]],
        ];
    }
}
