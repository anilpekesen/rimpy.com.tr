<?php

namespace App\Livewire\Admin\Design;

use App\Models\Setting;
use App\Services\ActivityLogger;
use App\Support\Theme;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Sitenin tasarım jetonları.
 *
 * Burada değiştirilen bir renk ya da yazı tipi, o rengi jeton olarak
 * kullanan bütün widget'lara aynı anda yansır. Müşteri kurumsal kimliğini
 * tek ekrandan uygular; sayfa sayfa dolaşmak zorunda kalmaz.
 */
#[Layout('layouts.admin')]
class ThemeSettings extends Component
{
    public array $theme = [];

    /** Hazır renk şemaları — sıfırdan renk seçmek zorunda kalmasınlar. */
    public const PRESETS = [
        'kurumsal-mavi' => ['label' => 'Kurumsal Mavi', 'colors' => [
            'primary' => '#1a56db', 'secondary' => '#0f172a', 'accent' => '#0ea5e9',
            'heading' => '#0f172a', 'text' => '#334155', 'muted' => '#64748b',
            'background' => '#ffffff', 'surface' => '#f8fafc', 'border' => '#e2e8f0',
        ]],
        'endustriyel' => ['label' => 'Endüstriyel', 'colors' => [
            'primary' => '#ea580c', 'secondary' => '#1c1917', 'accent' => '#facc15',
            'heading' => '#1c1917', 'text' => '#44403c', 'muted' => '#78716c',
            'background' => '#ffffff', 'surface' => '#fafaf9', 'border' => '#e7e5e4',
        ]],
        'dogal-yesil' => ['label' => 'Doğal Yeşil', 'colors' => [
            'primary' => '#15803d', 'secondary' => '#14532d', 'accent' => '#ca8a04',
            'heading' => '#14532d', 'text' => '#3f3f46', 'muted' => '#71717a',
            'background' => '#ffffff', 'surface' => '#f7faf7', 'border' => '#e4e7e4',
        ]],
        'sofistike' => ['label' => 'Sofistike', 'colors' => [
            'primary' => '#0f172a', 'secondary' => '#334155', 'accent' => '#b45309',
            'heading' => '#0f172a', 'text' => '#3f3f46', 'muted' => '#71717a',
            'background' => '#fdfdfc', 'surface' => '#f5f5f4', 'border' => '#e7e5e4',
        ]],
    ];

    public const FONTS = [
        'Inter' => 'Inter — modern, çok okunaklı',
        'Manrope' => 'Manrope — yumuşak, kurumsal',
        'Figtree' => 'Figtree — sıcak, sade',
        'DM Sans' => 'DM Sans — geometrik',
        'Source Sans 3' => 'Source Sans — nötr',
        'Playfair Display' => 'Playfair Display — klasik serif',
        'Lora' => 'Lora — okunaklı serif',
        'IBM Plex Sans' => 'IBM Plex Sans — teknik',
    ];

    public function mount(): void
    {
        $this->theme = array_replace_recursive(Theme::DEFAULTS, Setting::get('theme', []) ?: []);
    }

    public function applyPreset(string $key): void
    {
        if (! isset(self::PRESETS[$key])) {
            return;
        }

        $this->theme['colors'] = array_replace($this->theme['colors'], self::PRESETS[$key]['colors']);
        $this->dispatch('notify', message: 'Renk şeması uygulandı. Kaydetmeyi unutmayın.');
    }

    public function save(): void
    {
        $this->validate([
            'theme.colors.*' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme.layout.container' => 'required|integer|min:600|max:1920',
            'theme.layout.radius' => 'required|integer|min:0|max:40',
            'theme.typography.base_size' => 'required|integer|min:12|max:22',
        ], [
            'theme.colors.*.regex' => 'Renkler #rrggbb biçiminde olmalıdır.',
            'theme.layout.container.min' => 'İçerik genişliği en az 600 piksel olmalı.',
        ]);

        Setting::putGroup('theme', $this->theme);
        ActivityLogger::log('theme.updated', null, 'Tema ayarları güncellendi');

        // Derlenmiş sayfa CSS'i tema değişkenlerine referans verdiği için
        // yeniden derleme gerekmez; tarayıcı yeni değerleri okur.
        $this->dispatch('notify', message: 'Tema kaydedildi. Sitede hemen görünür.');
    }

    public function render()
    {
        return view('livewire.admin.design.theme-settings', [
            'presets' => self::PRESETS,
            'fonts' => self::FONTS,
        ])->title('Tema ayarları');
    }
}
