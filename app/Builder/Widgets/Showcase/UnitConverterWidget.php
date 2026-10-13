<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;

/**
 * Hidrolik birim dönüştürücü: tork, basınç, kuvvet, debi, uzunluk ve
 * sıcaklık. Herhangi bir kutuya yazılan değer aynı gruptaki diğer
 * birimlere çevrilir. Ürün verisi kullanmaz; hesap tarayıcıda yapılır.
 */
class UnitConverterWidget extends Widget
{
    use SectionHead;

    public function key(): string { return 'unit_converter'; }
    public function label(): string { return 'Birim dönüştürücü'; }
    public function icon(): string { return 'settings'; }
    public function category(): string { return 'catalog'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => $this->headControls(
                'Mühendislik araçları', "Birim\ndönüştürücü",
                'Herhangi bir kutuya değer yazın; aynı gruptaki diğer birimler anında güncellenir.'
            ) + [
                'note' => Controls::textarea('Alt not', ['default' => 'Ton değerleri metrik tondur (1 t = 9,80665 kN). Kısa ton (US) ayrı satırda verilir.']),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);

        return ['settings' => $settings, 'groups' => self::groups($context->locale === 'tr')];
    }

    /**
     * Birim grupları. Çarpan, birimin grubun ilk birimine oranıdır
     * (1 ft·lb = 1,35582 N·m). Sıcaklık doğrusal olmadığı için çarpan
     * yerine ölçek adı taşır.
     */
    public static function groups(bool $tr): array
    {
        return [
            'torque' => [
                'label' => $tr ? 'Tork' : 'Torque',
                'start' => 1000,
                'note' => $tr ? 'Hidrolik tork anahtarı katalogları genellikle N·m ve ft·lb verir.' : 'Hydraulic torque wrench catalogues usually list N·m and ft·lb.',
                'units' => ['N·m' => 1, 'kN·m' => 1000, 'ft·lb' => 1.35582, 'in·lb' => 0.112985, 'kgf·m' => 9.80665, 'kgf·cm' => 0.0980665, 'daN·m' => 10],
            ],
            'pressure' => [
                'label' => $tr ? 'Basınç' : 'Pressure',
                'start' => 700,
                'note' => $tr ? 'Standart hidrolik el aletleri 700 bar (10.000 psi), cıvata germe aletleri 1.500 bar\'a kadar çalışır.' : 'Standard hydraulic tools work at 700 bar (10,000 psi); bolt tensioners up to 1,500 bar.',
                'units' => ['bar' => 1, 'psi' => 0.0689476, 'MPa' => 10, 'kPa' => 0.01, 'kgf/cm²' => 0.980665, 'atm' => 1.01325],
            ],
            'force' => [
                'label' => $tr ? 'Kuvvet / yük' : 'Force / load',
                'start' => 100,
                'note' => $tr ? 'Silindir kapasitesi ton olarak verilir (1 t ≈ 9,81 kN). Kısa ton (US) 907 kg\'dır.' : 'Cylinder capacity is given in tonnes (1 t ≈ 9.81 kN). A US short ton is 907 kg.',
                'units' => ['kN' => 1, 'N' => 0.001, ($tr ? 'ton (metrik)' : 'tonne (metric)') => 9.80665, ($tr ? 'ton (US kısa)' : 'short ton (US)') => 8.89644, 'kgf' => 0.00980665, 'lbf' => 0.00444822],
            ],
            'flow' => [
                'label' => $tr ? 'Debi' : 'Flow',
                'start' => 1,
                'note' => $tr ? 'Pompa debisi genellikle yüksek basınç kademesinde L/dk olarak verilir.' : 'Pump flow is usually given in L/min at the high-pressure stage.',
                'units' => [($tr ? 'L/dk' : 'L/min') => 1, ($tr ? 'cm³/dk' : 'cm³/min') => 0.001, ($tr ? 'gal/dk (US)' : 'gal/min (US)') => 3.78541, ($tr ? 'in³/dk' : 'in³/min') => 0.0163871, ($tr ? 'L/sa' : 'L/h') => 1 / 60],
            ],
            'length' => [
                'label' => $tr ? 'Uzunluk' : 'Length',
                'start' => 25.4,
                'note' => $tr ? 'Somun ağzı ve strok ölçüleri için.' : 'For nut across-flats and stroke dimensions.',
                'units' => ['mm' => 1, 'cm' => 10, 'm' => 1000, ($tr ? 'inç' : 'in') => 25.4, 'ft' => 304.8],
            ],
            'temperature' => [
                'label' => $tr ? 'Sıcaklık' : 'Temperature',
                'start' => 60,
                'note' => $tr ? 'Hidrolik yağ çalışma sıcaklığı genellikle 60 °C\'yi geçmemelidir.' : 'Hydraulic oil should generally stay below 60 °C in operation.',
                'units' => ['°C' => 'C', '°F' => 'F', 'K' => 'K'],
            ],
        ];
    }
}
