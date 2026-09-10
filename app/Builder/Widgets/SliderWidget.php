<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class SliderWidget extends Widget
{
    public function key(): string { return 'slider'; }
    public function label(): string { return 'Slider'; }
    public function icon(): string { return 'gallery-horizontal'; }
    public function category(): string { return 'media'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Slaytlar', 'controls' => [
                'slides' => Controls::repeater('Slaytlar', [
                    'image' => Controls::media('Arka plan görseli'),
                    'eyebrow' => Controls::text('Üst etiket'),
                    'title' => Controls::text('Başlık', ['default' => 'Slayt başlığı']),
                    'text' => Controls::textarea('Açıklama'),
                    'button_text' => Controls::text('Buton yazısı'),
                    'link' => Controls::link('Buton bağlantısı'),
                    'align' => Controls::select('Metin hizası', [
                        'left' => 'Sola', 'center' => 'Ortaya', 'right' => 'Sağa',
                    ], ['default' => 'left']),
                ], [
                    'title_field' => 'title',
                    'add_label' => 'Slayt ekle',
                    'default' => [['title' => 'İlk slaydınız', 'text' => 'Kısa bir tanıtım metni.']],
                ]),
            ]],
            'settings' => ['label' => 'Davranış', 'controls' => [
                'height' => Controls::slider('Yükseklik', [
                    'max' => 1000, 'units' => ['px', 'vh'],
                    'default' => ['d' => ['size' => 620, 'unit' => 'px'], 'm' => ['size' => 460, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .slider', 'property' => 'height',
                ]),
                'autoplay' => Controls::toggle('Kendiliğinden geçsin', ['default' => true]),
                'interval' => Controls::slider('Geçiş süresi', [
                    'min' => 2, 'max' => 15, 'unit' => 'sn', 'responsive' => false,
                    'default' => ['size' => 6, 'unit' => 'sn'],
                    'condition' => ['autoplay' => true],
                ]),
                'transition' => Controls::select('Geçiş biçimi', [
                    'slide' => 'Kaydırarak', 'fade' => 'Belirerek',
                ], ['default' => 'slide']),
                'arrows' => Controls::toggle('Ok düğmeleri', ['default' => true]),
                'dots' => Controls::toggle('Nokta göstergesi', ['default' => true]),
                'ken_burns' => Controls::toggle('Görsele yavaş yakınlaşma efekti'),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'overlay' => Controls::color('Görsel üstü karartma', [
                    'selector' => '{{WRAPPER}} .slide-overlay', 'property' => 'background-color',
                    'default' => 'rgba(0,0,0,0.35)',
                    'hint' => 'Yazının okunabilmesi için görseli hafifçe karartır.',
                ]),
                'title_color' => Controls::color('Başlık rengi', ['selector' => '{{WRAPPER}} .slide-title', 'default' => '#ffffff']),
                'title_typography' => Controls::typography('Başlık yazısı', ['selector' => '{{WRAPPER}} .slide-title']),
                'text_color' => Controls::color('Metin rengi', ['selector' => '{{WRAPPER}} .slide-text', 'default' => 'rgba(255,255,255,.9)']),
                'text_typography' => Controls::typography('Metin yazısı', ['selector' => '{{WRAPPER}} .slide-text']),
                'content_width' => Controls::slider('Metin alanı genişliği', [
                    'max' => 1000, 'default' => ['d' => ['size' => 620, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .slide-content', 'property' => 'max-width',
                ]),
            ]],
        ];
    }
}
