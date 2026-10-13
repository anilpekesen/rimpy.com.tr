<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Widget;

/** Sarı-siyah ikaz şeritli kapanış çağrısı; düğmesi teklif sepetini açar. */
class CtaBandWidget extends Widget
{
    public function key(): string { return 'cta_band'; }
    public function label(): string { return 'Teklif çağrısı'; }
    public function icon(): string { return 'target'; }
    public function category(): string { return 'showcase'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'title' => Controls::textarea('Başlık', ['default' => "Projeniz\niçin teklif\nalın.", 'translatable' => true]),
                'text' => Controls::textarea('Açıklama', ['default' => 'Hangi ürünün uygun olduğundan emin değil misiniz? Cıvata ölçüsünü ve tork değerini söyleyin, doğru seti biz önerelim.', 'translatable' => true]),
                'button_text' => Controls::text('Buton (teklif sepetini açar)', ['default' => 'Teklif sepetini aç', 'translatable' => true]),
                'phone' => Controls::text('Telefon (boşsa site ayarlarındaki)'),
            ]],
        ];
    }
}
