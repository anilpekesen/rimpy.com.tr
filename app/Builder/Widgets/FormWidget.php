<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Models\Form;

class FormWidget extends Widget
{
    public function key(): string { return 'form'; }
    public function label(): string { return 'Form'; }
    public function icon(): string { return 'clipboard-list'; }
    public function category(): string { return 'form'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Form', 'controls' => [
                'form_id' => [
                    'type' => 'form_select',
                    'label' => 'Hangi form?',
                    'hint' => 'Formları “Formlar” bölümünden oluşturup buraya yerleştirirsiniz.',
                ],
                'title' => Controls::text('Üst başlık'),
                'description' => Controls::textarea('Açıklama'),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'label_color' => Controls::color('Etiket rengi', ['selector' => '{{WRAPPER}} .frm-label', 'default' => '@heading']),
                'input_bg' => Controls::color('Alan zemini', [
                    'selector' => '{{WRAPPER}} .frm-input', 'property' => 'background-color', 'default' => '#ffffff',
                ]),
                'input_color' => Controls::color('Alan yazısı', ['selector' => '{{WRAPPER}} .frm-input', 'default' => '@text']),
                'input_border' => Controls::border('Alan kenarlığı', ['selector' => '{{WRAPPER}} .frm-input']),
                'input_padding' => Controls::dimensions('Alan iç boşluğu', 'padding', ['selector' => '{{WRAPPER}} .frm-input']),
                'focus_color' => Controls::color('Seçili alan kenarlığı', [
                    'selector' => '{{WRAPPER}} .frm-input:focus', 'property' => 'border-color', 'default' => '@primary',
                ]),
                'button_bg' => Controls::color('Buton zemini', [
                    'selector' => '{{WRAPPER}} .frm-submit', 'property' => 'background-color', 'default' => '@primary',
                ]),
                'button_color' => Controls::color('Buton yazısı', ['selector' => '{{WRAPPER}} .frm-submit', 'default' => '#ffffff']),
                'button_width' => Controls::select('Buton genişliği', [
                    'auto' => 'İçeriğe göre', 'full' => 'Tam genişlik',
                ], ['default' => 'auto']),
                'gap' => Controls::slider('Alan aralığı', [
                    'max' => 48, 'default' => ['d' => ['size' => 16, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .frm-grid', 'property' => 'gap',
                ]),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        return [
            'settings' => $settings,
            'form' => Form::find($settings['form_id'] ?? null),
        ];
    }
}
