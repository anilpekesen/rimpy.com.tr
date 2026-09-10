<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Seo extends Component
{
    public array $seo = [];

    public function mount(): void
    {
        $this->seo = array_replace([
            'meta_title' => Locales::map(''),
            'meta_description' => Locales::map(''),
            'og_image_id' => null,
            'google_analytics' => '',
            'google_verification' => '',
            'noindex_site' => false,
        ], Setting::get('seo', []) ?: []);
    }

    public function save(): void
    {
        $this->validate([
            'seo.meta_description.*' => 'nullable|string|max:320',
            'seo.google_analytics' => 'nullable|string|max:40',
            'seo.google_verification' => 'nullable|string|max:120',
        ]);

        Setting::putGroup('seo', $this->seo);
        ActivityLogger::log('settings.updated', null, 'SEO ayarları güncellendi');

        $this->dispatch('notify', message: 'Kaydedildi.');
    }

    public function render()
    {
        return view('livewire.admin.settings.seo', ['locales' => Locales::all()])->title('SEO');
    }
}
