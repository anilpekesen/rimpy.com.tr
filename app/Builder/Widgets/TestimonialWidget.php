<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class TestimonialWidget extends Widget
{
    public function key(): string { return 'testimonial'; }
    public function label(): string { return 'Referans / Yorum'; }
    public function icon(): string { return 'quote'; }
    public function category(): string { return 'content'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'items' => Controls::repeater('Yorumlar', [
                    'quote' => Controls::textarea('Yorum', ['default' => 'Müşterinizin sizin hakkınızda söylediği söz.']),
                    'name' => Controls::text('Ad Soyad', ['default' => 'Ad Soyad']),
                    'role' => Controls::text('Ünvan / Firma'),
                    'avatar' => Controls::media('Fotoğraf'),
                    'rating' => Controls::select('Puan', [
                        '' => 'Gösterme', '5' => '5 yıldız', '4' => '4 yıldız', '3' => '3 yıldız',
                    ], ['default' => '']),
                ], ['title_field' => 'name', 'add_label' => 'Yorum ekle']),
                'layout' => Controls::select('Yerleşim', [
                    'grid' => 'Yan yana', 'carousel' => 'Kayan şerit', 'single' => 'Tek büyük',
                ], ['default' => 'grid']),
                'columns' => Controls::slider('Sütun sayısı', [
                    'min' => 1, 'max' => 4, 'unit' => '',
                    'default' => ['d' => ['size' => 3, 'unit' => ''], 'm' => ['size' => 1, 'unit' => '']],
                    'condition' => ['layout' => 'grid'],
                ]),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'card_bg' => Controls::background('Kart zemini', ['selector' => '{{WRAPPER}} .tst']),
                'card_radius' => Controls::dimensions('Kart köşeleri', 'border-radius', ['selector' => '{{WRAPPER}} .tst']),
                'card_padding' => Controls::dimensions('Kart iç boşluğu', 'padding', ['selector' => '{{WRAPPER}} .tst']),
                'quote_color' => Controls::color('Yorum rengi', ['selector' => '{{WRAPPER}} .tst-quote', 'default' => '@text']),
                'quote_typography' => Controls::typography('Yorum yazısı', ['selector' => '{{WRAPPER}} .tst-quote']),
                'name_color' => Controls::color('İsim rengi', ['selector' => '{{WRAPPER}} .tst-name', 'default' => '@heading']),
                'star_color' => Controls::color('Yıldız rengi', ['selector' => '{{WRAPPER}} .tst-stars', 'default' => '@accent']),
            ]],
        ];
    }
}
