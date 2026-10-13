<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Widget;

/**
 * Açılış bölümü: büyük başlık ve sağda kaydırdıkça "sıkılan" tork göstergesi.
 * Arka plana video konursa gösterge videonun üstünde kalır.
 */
class HeroGaugeWidget extends Widget
{
    public function key(): string { return 'hero_gauge'; }
    public function label(): string { return 'Açılış: tork göstergesi'; }
    public function icon(): string { return 'gauge'; }
    public function category(): string { return 'showcase'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'eyebrow' => Controls::text('Üst etiket', ['default' => 'Tork · Hidrolik · Cıvata Çözümleri']),
                'line1' => Controls::text('Başlık 1. satır', ['default' => 'Her cıvata']),
                'line2' => Controls::text('Başlık 2. satır (vurgulu ilk kelime)', ['default' => 'doğru torkla']),
                'line3' => Controls::text('Başlık 3. satır (kontur)', ['default' => 'sıkılır.']),
                'lead' => Controls::textarea('Açıklama', ['default' => 'Hidrolik tork anahtarları, 700 bar silindir ve pompalar. Stoktan teslim, Türkiye\'de servis ve kalibrasyon.']),
                'primary_text' => Controls::text('Ana buton', ['default' => 'Ürünleri keşfet']),
                'primary_link' => Controls::link('Ana buton bağlantısı'),
                'secondary_text' => Controls::text('İkinci buton (teklif sepetini açar)', ['default' => 'Teklif iste']),
                'specs' => Controls::repeater('Alt rakamlar', [
                    'value' => Controls::text('Değer', ['default' => '700']),
                    'label' => Controls::text('Etiket', ['default' => 'Bar sistem']),
                ], ['title_field' => 'value', 'default' => [
                    ['value' => '26.664', 'label' => 'N·m maks.'],
                    ['value' => '±%3', 'label' => 'Hassasiyet'],
                    ['value' => '700', 'label' => 'Bar sistem'],
                ]]),
            ]],
            'gauge' => ['label' => 'Gösterge', 'controls' => [
                'max' => Controls::number('Skala üst değeri', ['default' => 12000]),
                'unit' => Controls::text('Birim', ['default' => 'N·m']),
                'pressure' => Controls::number('Basınç üst değeri (bar)', ['default' => 700]),
                'rest' => Controls::slider('Açılışta dolma oranı (%)', ['default' => ['size' => 62, 'unit' => '%'], 'unit' => '%', 'units' => ['%'], 'responsive' => false, 'min' => 10, 'max' => 100]),
                'tag_top' => Controls::text('Sol üst etiket', ['default' => 'M64 · Cıvata']),
                'tag_bottom' => Controls::text('Sol alt etiket', ['default' => '±3% · Tekrarlanabilirlik']),
                'hint' => Controls::text('Alt ipucu', ['default' => 'Kaydırarak sıkın']),
            ]],
            'media' => ['label' => 'Arka plan', 'controls' => [
                'video' => Controls::media('Arka plan videosu (mp4/webm)', ['accept' => 'video']),
                'poster' => Controls::media('Video kapak görseli'),
                'gauge_with_video' => Controls::toggle('Video varken göstergeyi de göster', ['default' => false]),
                'dim' => Controls::slider('Sağ taraf karartma (%)', ['default' => ['size' => 25, 'unit' => '%'], 'unit' => '%', 'units' => ['%'], 'responsive' => false, 'min' => 0, 'max' => 90]),
            ]],
        ];
    }
}
