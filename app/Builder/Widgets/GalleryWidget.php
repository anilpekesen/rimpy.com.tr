<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class GalleryWidget extends Widget
{
    public function key(): string { return 'gallery'; }
    public function label(): string { return 'Galeri'; }
    public function icon(): string { return 'images'; }
    public function category(): string { return 'media'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'Görseller', 'controls' => [
                'images' => Controls::gallery('Görseller'),
                'layout' => Controls::select('Yerleşim', [
                    'grid' => 'Eşit ızgara',
                    'masonry' => 'Duvar örgüsü (farklı yükseklikler)',
                    'carousel' => 'Kayan şerit',
                ], ['default' => 'grid']),
                'columns' => Controls::slider('Sütun sayısı', [
                    'min' => 1, 'max' => 6, 'unit' => '',
                    'default' => ['d' => ['size' => 3, 'unit' => ''], 't' => ['size' => 2, 'unit' => ''], 'm' => ['size' => 1, 'unit' => '']],
                ]),
                'ratio' => Controls::select('En-boy oranı', [
                    '' => 'Kendi oranı', '1/1' => 'Kare', '4/3' => '4:3', '3/2' => '3:2', '16/9' => '16:9', '3/4' => 'Dikey',
                ], ['default' => '1/1', 'condition' => ['layout' => ['grid', 'carousel']]]),
                'lightbox' => Controls::toggle('Tıklayınca büyük göster', ['default' => true]),
                'captions' => Controls::toggle('Alt yazıları göster'),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'gap' => Controls::slider('Aralık', [
                    'max' => 60, 'default' => ['d' => ['size' => 12, 'unit' => 'px']],
                    'selector' => '{{WRAPPER}} .gallery', 'property' => 'gap',
                ]),
                'radius' => Controls::dimensions('Köşe yuvarlaklığı', 'border-radius', ['selector' => '{{WRAPPER}} .gallery-item']),
                'hover' => Controls::select('Üzerine gelince', [
                    '' => 'Bir şey olmasın', 'zoom' => 'Yakınlaş', 'lift' => 'Yüksel', 'overlay' => 'Karart',
                ], ['default' => 'zoom']),
            ]],
        ];
    }
}
