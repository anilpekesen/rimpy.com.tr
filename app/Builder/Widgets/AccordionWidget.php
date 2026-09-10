<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class AccordionWidget extends Widget
{
    public function key(): string { return 'accordion'; }
    public function label(): string { return 'Açılır kapanır (SSS)'; }
    public function icon(): string { return 'chevrons-down-up'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Başlıklar', 'controls' => [
                'items' => Controls::repeater('Bölümler', [
                    'title' => Controls::text('Soru / başlık', ['default' => 'Sıkça sorulan soru']),
                    'body' => Controls::rich('Cevap', ['default' => '<p>Cevabı buraya yazın.</p>']),
                    'open' => Controls::toggle('Açık başlasın'),
                ], [
                    'title_field' => 'title',
                    'add_label' => 'Soru ekle',
                    'default' => [
                        ['title' => 'İlk sorunuz', 'body' => '<p>Cevabı buraya yazın.</p>'],
                        ['title' => 'İkinci sorunuz', 'body' => '<p>Cevabı buraya yazın.</p>'],
                    ],
                ]),
                'single' => Controls::toggle('Aynı anda tek bölüm açık kalsın', ['default' => true]),
                'schema' => Controls::toggle('Google\'a SSS olarak bildir', [
                    'default' => true,
                    'hint' => 'Arama sonuçlarında soruların doğrudan görünmesini sağlayabilir.',
                ]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'title_color' => Controls::color('Başlık rengi', ['selector' => '{{WRAPPER}} .acc-title', 'default' => '@heading']),
                'title_typography' => Controls::typography('Başlık yazısı', ['selector' => '{{WRAPPER}} .acc-title']),
                'title_bg' => Controls::color('Başlık zemini', ['selector' => '{{WRAPPER}} .acc-head', 'property' => 'background-color']),
                'body_color' => Controls::color('Metin rengi', ['selector' => '{{WRAPPER}} .acc-body', 'default' => '@text']),
                'border_color' => Controls::color('Çizgi rengi', ['selector' => '{{WRAPPER}} .acc-item', 'property' => 'border-color', 'default' => '@border']),
                'icon_color' => Controls::color('İkon rengi', ['selector' => '{{WRAPPER}} .acc-icon', 'default' => '@primary']),
                'gap' => Controls::slider('Bölüm aralığı', ['max' => 32, 'selector' => '{{WRAPPER}} .acc', 'property' => 'gap']),
                'item_radius' => Controls::dimensions('Köşe yuvarlaklığı', 'border-radius', ['selector' => '{{WRAPPER}} .acc-item']),
            ]],
        ];
    }
}
