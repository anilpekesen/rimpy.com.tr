<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;

/**
 * Vitrin widget'larının ortak bölüm başlığı: küçük üst etiket, büyük başlık
 * ve yanında kısa açıklama. Her bölüm aynı ritimle açılsın diye tek yerde.
 */
trait SectionHead
{
    protected function headControls(string $eyebrow, string $title, string $text = ''): array
    {
        return [
            'eyebrow' => Controls::text('Üst etiket', ['default' => $eyebrow, 'translatable' => true]),
            'title' => Controls::textarea('Başlık', [
                'default' => $title, 'translatable' => true,
                'hint' => 'Satır atlamak için Enter. Bir kelimeyi *yıldız* içine alırsanız vurgu renginde görünür.',
            ]),
            'text' => Controls::textarea('Açıklama', ['default' => $text, 'translatable' => true]),
        ];
    }
}
