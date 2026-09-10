<?php

namespace App\Builder\Widgets;

use App\Builder\Controls;
use App\Builder\Widget;

class ImageWidget extends Widget
{
    public function key(): string { return 'image'; }
    public function label(): string { return 'Görsel'; }
    public function icon(): string { return 'image'; }
    public function category(): string { return 'media'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'image' => Controls::media('Görsel'),
                'size' => Controls::select('Yüklenecek boyut', [
                    'medium' => 'Orta (1024px) — sayfa içi',
                    'large' => 'Büyük (1920px) — geniş alanlar',
                    'small' => 'Küçük (640px) — küçük kartlar',
                    'original' => 'Orijinal',
                ], ['default' => 'large', 'hint' => 'Küçük boyut seçmek sayfayı hızlandırır.']),
                'caption' => Controls::text('Alt yazı'),
                'link' => Controls::link('Tıklanınca'),
                'lightbox' => Controls::toggle('Tıklayınca büyük göster', ['default' => false]),
                'lazy' => Controls::toggle('Görünür olunca yükle', ['default' => true,
                    'hint' => 'Sayfanın en üstündeki görsellerde kapatın, diğerlerinde açık kalsın.']),
            ]],
            'style' => ['label' => 'Görünüm', 'controls' => [
                'align' => Controls::choose('Hizalama', Controls::$alignments, [
                    'selector' => '{{WRAPPER}}', 'property' => 'text-align',
                ]),
                'width' => Controls::slider('Genişlik', [
                    'max' => 1200, 'units' => ['px', '%', 'vw'], 'unit' => '%',
                    'selector' => '{{WRAPPER}} .img-frame', 'property' => 'width',
                ]),
                'ratio' => Controls::select('En-boy oranı', [
                    '' => 'Görselin kendi oranı',
                    '1/1' => 'Kare (1:1)', '4/3' => '4:3', '3/2' => '3:2',
                    '16/9' => '16:9', '21/9' => '21:9', '3/4' => 'Dikey (3:4)', '9/16' => 'Dikey (9:16)',
                ], ['selector' => '{{WRAPPER}} img', 'property' => 'aspect-ratio']),
                'fit' => Controls::select('Alana sığdırma', [
                    'cover' => 'Alanı doldur (kenarlardan kırpar)',
                    'contain' => 'Tamamı görünsün',
                ], ['default' => 'cover', 'selector' => '{{WRAPPER}} img', 'property' => 'object-fit']),
                'radius' => Controls::dimensions('Köşe yuvarlaklığı', 'border-radius', ['selector' => '{{WRAPPER}} .img-frame']),
                'hover' => Controls::select('Üzerine gelince', [
                    '' => 'Bir şey olmasın',
                    'zoom' => 'Yakınlaş',
                    'lift' => 'Hafifçe yüksel',
                    'grayscale' => 'Renkten gri‑tona geç',
                ]),
            ]],
        ];
    }
}
