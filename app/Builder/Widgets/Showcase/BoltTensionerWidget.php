<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Support\ProductCatalog;

/**
 * Hidrolik cıvata germe hesabı: hedef kalıcı ön yükten yük aktarım
 * katsayısı (LTF) ile alete uygulanacak yükü, oradan da pompa basıncını
 * bulur. Kurallar Nord-Lock / Boltight hesap föyündeki gibidir: LTF en az
 * 1,10; germe anındaki gerilme akmanın %95'ini geçmez; %50 kapsamada ilk
 * tur basıncı son turun 1,25 katıdır. Uygun aletler katalogdan gelir.
 */
class BoltTensionerWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'bolt_tensioner'; }
    public function label(): string { return 'Cıvata germe hesabı'; }
    public function icon(): string { return 'gauge'; }
    public function category(): string { return 'catalog'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                'Mühendislik araçları', "Cıvata germe\nhesabı",
                'Cıvatayı, hedef ön yükü ve sıkma boyunu girin; germe aletine uygulanacak yükü ve pompa basıncını yük kaybı dahil hesaplayalım.'
            ) + [
                'category' => Controls::text('Germe aleti kategorisi (adres)', ['default' => 'civata-gerdirme']),
                'request_url' => Controls::text('Uygulama talep formu adresi', ['hint' => 'Boşsa buton gösterilmez. Örn. /tr/civata-germe-talep-formu']),
                'note' => Controls::textarea('Alt not', ['default' => 'Yük aktarım katsayısı LTF = 1,01 + çap / sıkma boyu (en az 1,10) ile hesaplanır. Sonuç bir yaklaşımdır; kalıcı yükün kesin bilinmesi gereken bağlantılarda germe öncesi ve sonrası cıvata uzaması ölçülmelidir.']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        $tensioners = ProductCatalog::inCategory($settings['category'] ?? 'civata-gerdirme', $context->locale)
            ->filter(fn ($p) => $p['germe_yuk_kn'] && $p['basinc_bar'])
            ->values();

        return compact('settings', 'tensioners');
    }
}
