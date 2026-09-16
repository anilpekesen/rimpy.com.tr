<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class General extends Component
{
    public array $general = [];

    public array $contact = [];

    public array $social = [];

    public function mount(): void
    {
        $this->general = array_replace([
            'site_name' => '', 'tagline' => Locales::map(''),
            'logo_id' => null, 'logo_light_id' => null, 'favicon_id' => null,
        ], Setting::get('general', []) ?: []);

        $this->contact = array_replace([
            'email' => '', 'phone' => '', 'mobile' => '', 'whatsapp' => '', 'fax' => '',
            'address' => Locales::map(''), 'working_hours' => Locales::map(''),
        ], Setting::get('contact', []) ?: []);

        $this->social = array_replace([
            'facebook' => '', 'instagram' => '', 'linkedin' => '', 'x' => '', 'youtube' => '',
        ], Setting::get('social', []) ?: []);
    }

    public function save(): void
    {
        $this->validate([
            'general.site_name' => 'required|string|max:120',
            'contact.email' => 'nullable|email|max:190',
            'contact.phone' => 'nullable|string|max:40',
            'contact.mobile' => 'nullable|string|max:40',
            'contact.whatsapp' => 'nullable|string|max:40',
            'contact.fax' => 'nullable|string|max:40',
            'social.*' => 'nullable|url|max:255',
        ], [
            'general.site_name.required' => 'Site adı zorunludur.',
            'contact.email.email' => 'Geçerli bir e-posta adresi girin.',
            'social.*.url' => 'Sosyal medya adresleri tam bağlantı olmalıdır (https:// ile başlamalı).',
        ]);

        Setting::putGroup('general', $this->general);
        Setting::putGroup('contact', $this->contact);
        Setting::putGroup('social', $this->social);

        ActivityLogger::log('settings.updated', null, 'Genel ayarlar güncellendi');

        $this->dispatch('notify', message: 'Ayarlar kaydedildi.');
    }

    public function render()
    {
        return view('livewire.admin.settings.general', ['locales' => Locales::all()])
            ->title('Genel ayarlar');
    }
}
