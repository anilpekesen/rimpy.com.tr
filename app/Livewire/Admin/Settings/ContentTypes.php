<?php

namespace App\Livewire\Admin\Settings;

use App\Models\ContentType;
use App\Models\Taxonomy;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * İçerik türü tanımlayıcı.
 *
 * "Referanslar", "Projeler", "Ekip" gibi yeni bir içerik grubu açmak için
 * kod yazılmaz: buradan tanımlanır, panel ve ön yüz kendini ona göre kurar.
 */
#[Layout('layouts.admin')]
class ContentTypes extends Component
{
    public ?int $editingId = null;

    public array $form = [];

    public const ICONS = [
        'box' => 'Kutu', 'newspaper' => 'Gazete', 'image' => 'Görsel',
        'users' => 'Ekip', 'file-text' => 'Belge', 'blocks' => 'Bloklar',
    ];

    public const SUPPORTS = [
        'image' => 'Kapak görseli',
        'gallery' => 'Görsel galerisi',
        'excerpt' => 'Kısa açıklama',
        'body' => 'Zengin metin içeriği',
        'builder' => 'Sayfa kurucusuyla serbest tasarım',
        'seo' => 'Arama motoru alanları',
        'taxonomies' => 'Kategori / etiket',
    ];

    public function mount(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->form = [
            'key' => '',
            'name' => Locales::map(''),
            'plural' => Locales::map(''),
            'route_prefix' => Locales::map(''),
            'icon' => 'box',
            'has_archive' => true,
            'supports' => ['image', 'excerpt', 'body', 'seo', 'taxonomies'],
            'fields' => [],
        ];
    }

    public function edit(int $id): void
    {
        $type = ContentType::findOrFail($id);

        $this->editingId = $id;
        $this->form = [
            'key' => $type->key,
            'name' => array_replace(Locales::map(''), $type->name ?? []),
            'plural' => array_replace(Locales::map(''), $type->plural ?? []),
            'route_prefix' => array_replace(Locales::map(''), $type->route_prefix ?? []),
            'icon' => $type->icon,
            'has_archive' => $type->has_archive,
            'supports' => $type->supports ?? [],
            'fields' => $type->fields ?? [],
        ];
    }

    public function addField(): void
    {
        $this->form['fields'][] = [
            'key' => 'alan-'.(count($this->form['fields']) + 1),
            'type' => 'text',
            'label' => Locales::map('Yeni alan'),
            'required' => false,
        ];
    }

    public function removeField(int $index): void
    {
        unset($this->form['fields'][$index]);
        $this->form['fields'] = array_values($this->form['fields']);
    }

    public function save(): void
    {
        $default = Locales::default();

        $this->validate([
            'form.name.'.$default => 'required|string|max:60',
            'form.plural.'.$default => 'required|string|max:60',
            'form.key' => 'required|alpha_dash|max:40|unique:content_types,key'.($this->editingId ? ','.$this->editingId : ''),
            'form.fields.*.key' => 'required|alpha_dash|max:40',
        ], [
            'form.name.'.$default.'.required' => 'Tekil adı girin (örnek: Ürün).',
            'form.plural.'.$default.'.required' => 'Çoğul adı girin (örnek: Ürünler).',
            'form.key.required' => 'Bir anahtar girin.',
            'form.key.unique' => 'Bu anahtar zaten kullanılıyor.',
            'form.key.alpha_dash' => 'Anahtar yalnızca harf, rakam ve tire içerebilir.',
        ]);

        // Adres öneki boş bırakıldıysa çoğul addan üretilir.
        $prefixes = $this->form['route_prefix'];

        foreach (Locales::codes() as $code) {
            if (blank($prefixes[$code] ?? null)) {
                $prefixes[$code] = Str::slug($this->form['plural'][$code] ?? $this->form['key']) ?: $this->form['key'];
            }
        }

        $attributes = [
            'key' => $this->form['key'],
            'name' => $this->form['name'],
            'plural' => $this->form['plural'],
            'route_prefix' => $prefixes,
            'icon' => $this->form['icon'],
            'has_archive' => (bool) $this->form['has_archive'],
            'supports' => array_values($this->form['supports']),
            'fields' => array_values($this->form['fields']),
        ];

        if ($this->editingId) {
            $type = ContentType::findOrFail($this->editingId);
            $type->update($attributes);
            ActivityLogger::updated($type, "İçerik türü güncellendi: {$type->label('tr')}");
        } else {
            $type = ContentType::create($attributes + [
                'sort' => (ContentType::max('sort') ?? 0) + 1,
                'is_active' => true,
            ]);

            // Kategori desteği açıksa hemen bir sınıflandırma açılır ki
            // kullanıcı ikinci bir ekran aramak zorunda kalmasın.
            if (in_array('taxonomies', $attributes['supports'], true)) {
                Taxonomy::create([
                    'content_type_id' => $type->id,
                    'key' => 'kategori',
                    'name' => Locales::map('Kategori'),
                    'plural' => Locales::map('Kategoriler'),
                    'is_hierarchical' => true,
                    'route_prefix' => Locales::map('kategori'),
                ]);
            }

            ActivityLogger::created($type, "İçerik türü oluşturuldu: {$type->label('tr')}");
        }

        $this->reset('editingId');
        $this->resetForm();

        $this->dispatch('notify', message: 'Kaydedildi. Sol menüde görebilirsiniz.');
    }

    public function toggleActive(int $id): void
    {
        $type = ContentType::findOrFail($id);
        $type->update(['is_active' => ! $type->is_active]);

        $this->dispatch('notify', message: $type->is_active ? 'Tür etkin.' : 'Tür gizlendi. İçerikleri silinmedi.');
    }

    public function delete(int $id): void
    {
        $type = ContentType::withCount('items')->findOrFail($id);

        if ($type->is_system) {
            $this->dispatch('notify', message: 'Bu tür sistem tarafından kullanılıyor ve silinemez.');

            return;
        }

        if ($type->items_count > 0) {
            $this->dispatch('notify', message: "Bu türde {$type->items_count} kayıt var. Önce onları silin ya da türü gizleyin.");

            return;
        }

        ActivityLogger::deleted($type, "İçerik türü silindi: {$type->label('tr')}");
        $type->delete();

        $this->dispatch('notify', message: 'İçerik türü silindi.');
    }

    public function render()
    {
        return view('livewire.admin.settings.content-types', [
            'types' => ContentType::with('taxonomies')->withCount('items')->orderBy('sort')->get(),
            'icons' => self::ICONS,
            'supportOptions' => self::SUPPORTS,
            'locales' => Locales::all(),
        ])->title('İçerik türleri');
    }
}
