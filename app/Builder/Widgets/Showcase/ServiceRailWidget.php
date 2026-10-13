<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;

/**
 * Hizmet kartları yatay bir şeritte. Masaüstünde sayfa aşağı kaydırıldıkça
 * şerit yana akar; telefonda kartlar alt alta dizilir.
 */
class ServiceRailWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'service_rail'; }
    public function label(): string { return 'Hizmet şeridi'; }
    public function icon(): string { return 'settings'; }
    public function category(): string { return 'showcase'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls('04 — Hizmetler', 'Aletten fazlası.') + [
                'items' => Controls::repeater('Kartlar', [
                    'title' => Controls::text('Başlık', ['default' => 'Hizmet']),
                    'text' => Controls::textarea('Açıklama'),
                    'tag' => Controls::text('Sağ üst etiket'),
                    'image' => Controls::media('Görsel'),
                    'link' => Controls::link('Bağlantı'),
                ], ['title_field' => 'title', 'default' => [
                    ['title' => 'Tork anahtarı kiralama', 'tag' => 'Günlük · Haftalık', 'text' => 'Duruş bakımları ve şantiyeler için kalibreli hidrolik tork setleri; pompa ve hortumla birlikte.'],
                    ['title' => 'Kalibrasyon ve tamir', 'tag' => 'ISO 6789', 'text' => 'Tüm marka tork aletleri için izlenebilir kalibrasyon sertifikası; 12 ayda bir hatırlatma.'],
                    ['title' => 'Bina ve köprü kaldırma', 'tag' => 'Senkron', 'text' => 'Çok noktalı senkron hidrolik sistemlerle milimetrik kaldırma ve mesnet değişimi.'],
                    ['title' => 'Yerinde cıvata sıkma', 'tag' => 'Sahada', 'text' => 'Ekibimiz flanş ve kule bağlantılarını prosedüre uygun sıkar, raporlar.'],
                    ['title' => 'Operatör eğitimi', 'tag' => 'Uygulamalı', 'text' => 'Güvenli kullanım ve cıvata sıkma prosedürleri üzerine sahada eğitim.'],
                ]]),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        // Tekrarlayıcı içindeki görsel ve bağlantılar ayrıca çözülür.
        $settings['items'] = array_map(fn ($item) => [
            'image' => $context->media($item['image'] ?? null),
            'href' => $context->link($item['link'] ?? null),
        ] + $item, $settings['items'] ?? []);

        return compact('settings');
    }
}
