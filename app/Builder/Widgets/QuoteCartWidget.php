<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;

/**
 * Üst başlığa konan teklif sepeti düğmesi: sepetteki ürün sayısını gösterir
 * ve tıklanınca sepeti açar. Teklif formu panelde tanımlı değilse hiçbir
 * şey çizmez.
 */
class QuoteCartWidget extends Widget
{
    public function key(): string { return 'quote_cart'; }
    public function label(): string { return 'Teklif sepeti'; }
    public function icon(): string { return 'cart'; }
    public function category(): string { return 'site'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'text' => Controls::text('Yazı', ['default' => 'Teklif sepeti', 'translatable' => true]),
                'show_text' => Controls::toggle('Yazıyı göster', ['default' => true]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', Controls::$alignments, [
                    'selector' => '{{WRAPPER}}', 'property' => 'text-align',
                ]),
                'bg' => Controls::color('Zemin rengi', [
                    'selector' => '{{WRAPPER}} .qcb', 'property' => 'background-color', 'default' => '@primary',
                ]),
                'color' => Controls::color('Yazı rengi', ['selector' => '{{WRAPPER}} .qcb', 'default' => '#111111']),
                'radius' => Controls::dimensions('Köşe yuvarlaklığı', 'border-radius', ['selector' => '{{WRAPPER}} .qcb']),
                'typography' => Controls::typography(options: ['selector' => '{{WRAPPER}} .qcb']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        return parent::data($element, $context) + [
            'enabled' => \App\Models\Form::where('key', config('cms.quote.form_key'))->exists(),
            'count' => app(\App\Services\QuoteCart::class)->count(),
        ];
    }
}
