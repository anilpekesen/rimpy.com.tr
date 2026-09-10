<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Language;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Languages extends Component
{
    public bool $adding = false;

    public string $code = '';

    public string $name = '';

    public string $nativeName = '';

    /** Sık kullanılan diller — kullanıcı kod ezberlemek zorunda kalmasın. */
    public const COMMON = [
        'tr' => ['Türkçe', 'Türkçe'], 'en' => ['İngilizce', 'English'],
        'de' => ['Almanca', 'Deutsch'], 'ar' => ['Arapça', 'العربية'],
        'ru' => ['Rusça', 'Русский'], 'fr' => ['Fransızca', 'Français'],
        'es' => ['İspanyolca', 'Español'], 'it' => ['İtalyanca', 'Italiano'],
    ];

    public function updatedCode(): void
    {
        if (isset(self::COMMON[$this->code])) {
            [$this->name, $this->nativeName] = self::COMMON[$this->code];
        }
    }

    public function add(): void
    {
        $this->validate([
            'code' => 'required|string|size:2|alpha|unique:languages,code',
            'name' => 'required|string|max:60',
            'nativeName' => 'required|string|max:60',
        ], [
            'code.unique' => 'Bu dil zaten ekli.',
            'code.size' => 'Dil kodu iki harf olmalıdır (tr, en, de gibi).',
        ]);

        $language = Language::create([
            'code' => $this->code,
            'name' => $this->name,
            'native_name' => $this->nativeName,
            'direction' => in_array($this->code, ['ar', 'he', 'fa'], true) ? 'rtl' : 'ltr',
            'is_default' => Language::count() === 0,
            'is_active' => true,
            'sort' => (Language::max('sort') ?? 0) + 1,
        ]);

        ActivityLogger::created($language, "Dil eklendi: {$this->name}");

        $this->reset('adding', 'code', 'name', 'nativeName');
        $this->dispatch('notify', message: 'Dil eklendi. İçerikleri bu dilde de doldurmayı unutmayın.');
    }

    public function setDefault(int $id): void
    {
        Language::query()->update(['is_default' => false]);
        $language = Language::findOrFail($id);
        $language->update(['is_default' => true, 'is_active' => true]);

        ActivityLogger::updated($language, "Varsayılan dil: {$language->name}");
        Locales::forget();

        $this->dispatch('notify', message: 'Varsayılan dil güncellendi.');
    }

    public function toggleActive(int $id): void
    {
        $language = Language::findOrFail($id);

        if ($language->is_default) {
            $this->dispatch('notify', message: 'Varsayılan dil kapatılamaz. Önce başka bir dili varsayılan yapın.');

            return;
        }

        $language->update(['is_active' => ! $language->is_active]);
        Locales::forget();

        $this->dispatch('notify', message: $language->is_active ? 'Dil sitede görünür.' : 'Dil sitede gizlendi.');
    }

    public function delete(int $id): void
    {
        $language = Language::findOrFail($id);

        if ($language->is_default) {
            $this->dispatch('notify', message: 'Varsayılan dil silinemez.');

            return;
        }

        ActivityLogger::deleted($language, "Dil silindi: {$language->name}");
        $language->delete();
        Locales::forget();

        $this->dispatch('notify', message: 'Dil kaldırıldı. Bu dildeki içerikler veritabanında duruyor.');
    }

    public function render()
    {
        return view('livewire.admin.settings.languages', [
            'languages' => Language::orderBy('sort')->get(),
            'common' => self::COMMON,
        ])->title('Diller');
    }
}
