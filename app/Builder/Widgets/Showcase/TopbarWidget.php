<?php

namespace App\Builder\Widgets\Showcase;

use App\Builder\Controls;
use App\Builder\Element;
use App\Builder\RenderContext;
use App\Builder\Widget;
use App\Models\Setting;

/**
 * Üst çubuk: çalışma saatleri, adres, telefon, e-posta, sosyal ağlar ve dil.
 *
 * Bilgiler Ayarlar → Genel'den okunur; müşteri telefonu ya da adresi tek
 * yerde değiştirir, üst çubuk ve alt bilgi birlikte güncellenir. Boş
 * bırakılan bilgi ve adresi girilmemiş sosyal ağ gösterilmez.
 */
class TopbarWidget extends Widget
{
    public const NETWORKS = [
        'facebook' => ['facebook', 'Facebook'],
        'youtube' => ['youtube', 'YouTube'],
        'x' => ['x-twitter', 'X'],
        'linkedin' => ['linkedin', 'LinkedIn'],
        'instagram' => ['instagram', 'Instagram'],
    ];

    public function key(): string { return 'topbar'; }
    public function label(): string { return 'Üst çubuk'; }
    public function icon(): string { return 'phone'; }
    public function category(): string { return 'site'; }

    public function controls(): array
    {
        return [
            'content' => ['label' => 'İçerik', 'controls' => [
                'show_hours' => Controls::toggle('Çalışma saatleri', ['default' => true]),
                'show_address' => Controls::toggle('Adres', ['default' => true]),
                'show_phone' => Controls::toggle('Telefon', ['default' => true]),
                'show_email' => Controls::toggle('E-posta', ['default' => true]),
                'show_social' => Controls::toggle('Sosyal ağlar', ['default' => true]),
                'show_language' => Controls::toggle('Dil seçici', ['default' => true]),
            ]],
        ];
    }

    public function data(Element $element, RenderContext $context): array
    {
        $settings = $this->resolved($element, $context);
        $contact = Setting::get('contact', []) ?: [];
        $localized = fn ($value) => is_array($value) ? ($value[$context->locale] ?? reset($value) ?: null) : $value;

        $social = collect(Setting::get('social', []) ?: [])
            ->filter(fn ($url, $key) => filled($url) && isset(self::NETWORKS[$key]))
            ->sortBy(fn ($url, $key) => array_search($key, array_keys(self::NETWORKS), true))
            ->map(fn ($url, $key) => ['url' => $url, 'icon' => self::NETWORKS[$key][0], 'label' => self::NETWORKS[$key][1]]);

        return [
            'settings' => $settings,
            'hours' => $localized($contact['working_hours'] ?? null),
            'address' => $localized($contact['address'] ?? null),
            'phone' => $contact['phone'] ?? null,
            'email' => $contact['email'] ?? null,
            'social' => $social,
        ];
    }
}
