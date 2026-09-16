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

    public string $presetKey = '';

    /** Sık kullanılan kurumsal modüller için güvenli başlangıç şemaları. */
    public const PRESETS = [
        'blog' => ['name' => ['tr' => 'Makale', 'en' => 'Article'], 'plural' => ['tr' => 'Blog', 'en' => 'Blog'], 'icon' => 'newspaper', 'supports' => ['image', 'excerpt', 'body', 'seo', 'taxonomies'], 'fields' => []],
        'projeler' => ['name' => ['tr' => 'Proje', 'en' => 'Project'], 'plural' => ['tr' => 'Projeler', 'en' => 'Projects'], 'icon' => 'blocks', 'supports' => ['image', 'gallery', 'excerpt', 'body', 'seo', 'taxonomies'], 'fields' => [['key' => 'musteri', 'type' => 'text', 'label' => ['tr' => 'Müşteri', 'en' => 'Client']], ['key' => 'konum', 'type' => 'text', 'label' => ['tr' => 'Konum', 'en' => 'Location']], ['key' => 'yil', 'type' => 'number', 'label' => ['tr' => 'Yıl', 'en' => 'Year']]]],
        'referanslar' => ['name' => ['tr' => 'Referans', 'en' => 'Reference'], 'plural' => ['tr' => 'Referanslar', 'en' => 'References'], 'icon' => 'users', 'supports' => ['image', 'excerpt', 'seo', 'taxonomies'], 'fields' => [['key' => 'web_adresi', 'type' => 'url', 'label' => ['tr' => 'Web adresi', 'en' => 'Website']], ['key' => 'sektor', 'type' => 'text', 'label' => ['tr' => 'Sektör', 'en' => 'Industry']]]],
        'ofisler' => ['name' => ['tr' => 'Ofis', 'en' => 'Office'], 'plural' => ['tr' => 'Ofisler', 'en' => 'Offices'], 'icon' => 'blocks', 'supports' => ['image', 'body', 'seo'], 'fields' => [['key' => 'adres', 'type' => 'textarea', 'label' => ['tr' => 'Adres', 'en' => 'Address']], ['key' => 'telefon', 'type' => 'text', 'label' => ['tr' => 'Telefon', 'en' => 'Phone']], ['key' => 'eposta', 'type' => 'text', 'label' => ['tr' => 'E-posta', 'en' => 'Email']], ['key' => 'harita', 'type' => 'url', 'label' => ['tr' => 'Harita bağlantısı', 'en' => 'Map link']]]],
        'sss' => ['name' => ['tr' => 'Soru', 'en' => 'Question'], 'plural' => ['tr' => 'Sık Sorulan Sorular', 'en' => 'FAQ'], 'icon' => 'file-text', 'supports' => ['body', 'taxonomies', 'seo'], 'fields' => []],
        'duyurular' => ['name' => ['tr' => 'Duyuru', 'en' => 'Announcement'], 'plural' => ['tr' => 'Duyurular', 'en' => 'Announcements'], 'icon' => 'newspaper', 'supports' => ['image', 'excerpt', 'body', 'seo'], 'fields' => [['key' => 'bitis_tarihi', 'type' => 'date', 'label' => ['tr' => 'Bitiş tarihi', 'en' => 'End date']], ['key' => 'dosya', 'type' => 'file', 'label' => ['tr' => 'Ek dosya', 'en' => 'Attachment']]]],
        'galeriler' => ['name' => ['tr' => 'Galeri', 'en' => 'Gallery'], 'plural' => ['tr' => 'Galeriler', 'en' => 'Galleries'], 'icon' => 'image', 'supports' => ['image', 'gallery', 'excerpt', 'seo', 'taxonomies'], 'fields' => []],
        'ekip' => ['name' => ['tr' => 'Ekip üyesi', 'en' => 'Team member'], 'plural' => ['tr' => 'Ekip', 'en' => 'Team'], 'icon' => 'users', 'supports' => ['image', 'body', 'seo'], 'fields' => [['key' => 'gorev', 'type' => 'text', 'label' => ['tr' => 'Görev', 'en' => 'Role']], ['key' => 'eposta', 'type' => 'text', 'label' => ['tr' => 'E-posta', 'en' => 'Email']]]],
    ];

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

    public function installPreset(): void
    {
        $preset = self::PRESETS[$this->presetKey] ?? null;

        if (! $preset) {
            $this->addError('presetKey', 'Eklemek istediğiniz hazır türü seçin.');

            return;
        }

        if (ContentType::where('key', $this->presetKey)->exists()) {
            $this->dispatch('notify', message: 'Bu içerik türü zaten kurulu.');

            return;
        }

        $localized = function (array $values): array {
            $result = [];

            foreach (Locales::codes() as $code) {
                $result[$code] = $values[$code] ?? $values['tr'] ?? reset($values);
            }

            return $result;
        };

        $fields = collect($preset['fields'])->map(function (array $field) use ($localized): array {
            return array_replace($field, [
                'required' => false,
                'label' => $localized($field['label']),
            ]);
        })->all();

        $this->editingId = null;
        $this->form = [
            'key' => $this->presetKey,
            'name' => $localized($preset['name']),
            'plural' => $localized($preset['plural']),
            'route_prefix' => $localized(array_map(fn ($label) => Str::slug($label), $preset['plural'])),
            'icon' => $preset['icon'],
            'has_archive' => true,
            'supports' => $preset['supports'],
            'fields' => $fields,
        ];

        $this->save();
        $this->presetKey = '';
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
            'presets' => self::PRESETS,
            'locales' => Locales::all(),
        ])->title('İçerik türleri');
    }
}
