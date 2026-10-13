<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;

/**
 * Flanş cıvatası sıkma sıralaması: cıvata sayısı ve hedef torktan çapraz
 * (yıldız) sırayı, tur bazında tork değerlerini ve adım adım oynatılan
 * flanş çizimini üretir. Ürün verisi kullanmaz; hesap tarayıcıda yapılır.
 */
class FlangeSequenceWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'flange_sequence'; }
    public function label(): string { return 'Flanş sıkma sıralaması'; }
    public function icon(): string { return 'target'; }
    public function category(): string { return 'catalog'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                'Mühendislik araçları', "Flanş sıkma\nsıralaması",
                'Cıvata sayısını ve hedef torku girin; çapraz sıralamayı, tur bazında tork değerlerini ve adım adım çizimi görün.'
            ) + [
                'bolts' => Controls::number('Başlangıç cıvata sayısı', ['default' => 16]),
                'torque' => Controls::number('Başlangıç hedef tork (N·m)', ['default' => 450]),
                'note' => Controls::textarea('Alt not', ['default' => 'Numaralar saat yönünde cıvata konumlarıdır (1 = saat 12 yönü). Değerler genel uygulamaya göre hesaplanır; contalı bağlantılarda conta üreticisinin prosedürü ve ASME PCC-1 esas alınmalıdır.']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        return ['settings' => $this->resolved($element, $context), 'flanges' => self::ASME_B16_5];
    }

    /**
     * ASME B16.5 boru flanşlarının cıvatalaması: sınıf => NPS => [cıvata
     * adedi, cıvata çapı]. Flanş seçilince cıvata sayısı buradan dolar.
     */
    public const ASME_B16_5 = [
        '150' => [
            '1/2' => [4, '1/2"'], '3/4' => [4, '1/2"'], '1' => [4, '1/2"'], '1-1/4' => [4, '1/2"'], '1-1/2' => [4, '1/2"'],
            '2' => [4, '5/8"'], '2-1/2' => [4, '5/8"'], '3' => [4, '5/8"'], '3-1/2' => [8, '5/8"'], '4' => [8, '5/8"'],
            '5' => [8, '3/4"'], '6' => [8, '3/4"'], '8' => [8, '3/4"'], '10' => [12, '7/8"'], '12' => [12, '7/8"'],
            '14' => [12, '1"'], '16' => [16, '1"'], '18' => [16, '1-1/8"'], '20' => [20, '1-1/8"'], '24' => [20, '1-1/4"'],
        ],
        '300' => [
            '1/2' => [4, '1/2"'], '3/4' => [4, '5/8"'], '1' => [4, '5/8"'], '1-1/4' => [4, '5/8"'], '1-1/2' => [4, '3/4"'],
            '2' => [8, '5/8"'], '2-1/2' => [8, '3/4"'], '3' => [8, '3/4"'], '3-1/2' => [8, '3/4"'], '4' => [8, '3/4"'],
            '5' => [8, '3/4"'], '6' => [12, '3/4"'], '8' => [12, '7/8"'], '10' => [16, '1"'], '12' => [16, '1-1/8"'],
            '14' => [20, '1-1/8"'], '16' => [20, '1-1/4"'], '18' => [24, '1-1/4"'], '20' => [24, '1-1/4"'], '24' => [24, '1-1/2"'],
        ],
        '600' => [
            '1/2' => [4, '1/2"'], '3/4' => [4, '5/8"'], '1' => [4, '5/8"'], '1-1/4' => [4, '5/8"'], '1-1/2' => [4, '3/4"'],
            '2' => [8, '5/8"'], '2-1/2' => [8, '3/4"'], '3' => [8, '3/4"'], '3-1/2' => [8, '7/8"'], '4' => [8, '7/8"'],
            '5' => [8, '1"'], '6' => [12, '1"'], '8' => [12, '1-1/8"'], '10' => [16, '1-1/4"'], '12' => [20, '1-1/4"'],
            '14' => [20, '1-3/8"'], '16' => [20, '1-1/2"'], '18' => [20, '1-5/8"'], '20' => [24, '1-5/8"'], '24' => [24, '1-7/8"'],
        ],
        '900' => [
            '1/2' => [4, '3/4"'], '3/4' => [4, '3/4"'], '1' => [4, '7/8"'], '1-1/4' => [4, '7/8"'], '1-1/2' => [4, '1"'],
            '2' => [8, '7/8"'], '2-1/2' => [8, '1"'], '3' => [8, '7/8"'], '4' => [8, '1-1/8"'], '5' => [8, '1-1/4"'],
            '6' => [12, '1-1/8"'], '8' => [12, '1-3/8"'], '10' => [16, '1-3/8"'], '12' => [20, '1-3/8"'], '14' => [20, '1-1/2"'],
            '16' => [20, '1-5/8"'], '18' => [20, '1-7/8"'], '20' => [20, '2"'], '24' => [20, '2-1/2"'],
        ],
        '1500' => [
            '1/2' => [4, '3/4"'], '3/4' => [4, '3/4"'], '1' => [4, '7/8"'], '1-1/4' => [4, '7/8"'], '1-1/2' => [4, '1"'],
            '2' => [8, '7/8"'], '2-1/2' => [8, '1"'], '3' => [8, '1-1/8"'], '4' => [8, '1-1/4"'], '5' => [8, '1-1/2"'],
            '6' => [12, '1-3/8"'], '8' => [12, '1-5/8"'], '10' => [12, '1-7/8"'], '12' => [16, '2"'], '14' => [16, '2-1/4"'],
            '16' => [16, '2-1/2"'], '18' => [16, '2-3/4"'], '20' => [16, '3"'], '24' => [16, '3-1/2"'],
        ],
        '2500' => [
            '1/2' => [4, '3/4"'], '3/4' => [4, '3/4"'], '1' => [4, '7/8"'], '1-1/4' => [4, '1"'], '1-1/2' => [4, '1-1/8"'],
            '2' => [8, '1"'], '2-1/2' => [8, '1-1/8"'], '3' => [8, '1-1/4"'], '4' => [8, '1-1/2"'], '5' => [8, '1-3/4"'],
            '6' => [8, '2"'], '8' => [12, '2"'], '10' => [12, '2-1/2"'], '12' => [12, '2-3/4"'],
        ],
    ];
}
