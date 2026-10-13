<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Widget;

/** Güven maddeleri: sertifika, servis, garanti… İkon, başlık, kısa metin. */
class TrustGridWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'trust_grid'; }
    public function label(): string { return 'Güven maddeleri'; }
    public function icon(): string { return 'shield'; }
    public function category(): string { return 'showcase'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                '06 — Neden RIMPY', "Kanıtı\nbelgede.",
                'Yeni bir marka olarak güveni sözle değil belgeyle kuruyoruz. Her ürünün sertifikası ürün sayfasında, indirmeye hazır.'
            ) + [
                'items' => Controls::repeater('Maddeler', [
                    'icon' => Controls::icon('İkon', ['default' => 'badge-check']),
                    'title' => Controls::text('Başlık'),
                    'text' => Controls::textarea('Açıklama'),
                ], ['title_field' => 'title', 'default' => [
                    ['icon' => 'badge-check', 'title' => 'Test sertifikası', 'text' => 'Her silindir ve pompa sevkiyattan önce basınç testinden geçer; rapor ürünle birlikte gelir.'],
                    ['icon' => 'award', 'title' => 'CE uygunluk', 'text' => 'Uygunluk beyanları ürün sayfasında PDF olarak indirilebilir.'],
                    ['icon' => 'target', 'title' => 'ISO 6789 kalibrasyon', 'text' => 'Tork anahtarları kalibrasyon sertifikasıyla teslim edilir; 12 ayda bir hatırlatırız.'],
                    ['icon' => 'settings', 'title' => 'Türkiye\'de servis', 'text' => 'Yedek parça stokta, tamir ve kalibrasyon kendi atölyemizde.'],
                    ['icon' => 'truck', 'title' => 'Stoktan teslim', 'text' => 'Stok durumu ve tahmini teslim süresi her ürün sayfasında açıkça yazar.'],
                    ['icon' => 'whatsapp', 'title' => 'Mühendise sor', 'text' => 'WhatsApp\'tan yazın, bir saat içinde mühendisimiz dönsün.'],
                ]]),
            ]],
        ];
    }
}
