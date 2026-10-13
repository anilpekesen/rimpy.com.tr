<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Widget;

/** Numaralı adımlar; ekrana girince üstteki çizgiler sırayla dolar. */
class StepsWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'steps'; }
    public function label(): string { return 'Adımlar'; }
    public function icon(): string { return 'list'; }
    public function category(): string { return 'showcase'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls('03 — Teklif süreci', "Dört adımda\nsahadasınız.") + [
                'items' => Controls::repeater('Adımlar', [
                    'title' => Controls::text('Başlık', ['default' => 'Adım']),
                    'text' => Controls::textarea('Açıklama'),
                ], ['title_field' => 'title', 'default' => [
                    ['title' => 'Seçin', 'text' => 'İhtiyacınız olan ürünleri teklif sepetine ekleyin, adetleri girin.'],
                    ['title' => 'Gönderin', 'text' => 'Firma ve iletişim bilgilerinizi bırakın. Üyelik gerekmez.'],
                    ['title' => 'Teklif alın', 'text' => 'Mühendisimiz uygulamanızı değerlendirip 24 saat içinde fiyat iletir.'],
                    ['title' => 'Sahada', 'text' => 'Stoktan hızlı sevkiyat; gerekirse yerinde kurulum ve operatör eğitimi.'],
                ]]),
            ]],
        ];
    }
}
