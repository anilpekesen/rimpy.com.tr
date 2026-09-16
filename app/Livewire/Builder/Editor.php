<?php

namespace App\Livewire\Builder;

use App\Builder\Controls;
use App\Builder\StyleCompiler;
use App\Builder\Tree;
use App\Builder\TreeEditor;
use App\Builder\WidgetRegistry;
use App\Http\Controllers\Admin\BuilderPreviewController;
use App\Models\ContentItem;
use App\Models\Page;
use App\Models\PageRevision;
use App\Models\Template;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Sayfa kurucusu.
 *
 * Sol tarafta öğe kütüphanesi ve katman ağacı, ortada sitenin kendisiyle
 * çizilen önizleme çerçevesi (BuilderPreviewController), sağda seçili öğenin
 * ayarları bulunur. Ağaç tek bir dizi olarak taşınır; her
 * değişiklikte yeniden çizilir ve CSS yeniden derlenir. Kaydetmeden önceki
 * hâl geri alma yığınında tutulur.
 */
#[Layout('layouts.builder')]
class Editor extends Component
{
    /** Düzenlenen kayıt: sayfa, içerik ya da şablon. */
    public string $subjectType = 'page';

    public int $subjectId;

    public string $locale = 'tr';

    /** Kurucu ağacı (veritabanındaki JSON'un dizi hâli). */
    public array $tree = [];

    public ?string $selectedId = null;

    /** Tuval önizleme cihazı: d (masaüstü), t (tablet), m (telefon). */
    public string $device = 'd';

    public string $leftTab = 'widgets';

    public string $settingsTab = 'content';

    public bool $dirty = false;

    /** Kütüphanede arama */
    public string $widgetSearch = '';

    public bool $showLayoutPicker = false;

    public ?string $layoutTargetId = null;

    // ── Yaşam döngüsü ────────────────────────────────────────────────────

    public function mount(?Page $page = null, ?ContentItem $item = null, ?Template $template = null, ?string $locale = null)
    {
        $subject = $page?->exists ? $page : ($item?->exists ? $item : $template);

        abort_unless($subject !== null, 404);

        $this->subjectType = match (true) {
            $subject instanceof Page => 'page',
            $subject instanceof ContentItem => 'content',
            default => 'template',
        };

        $this->subjectId = $subject->getKey();
        $this->locale = $locale && Locales::exists($locale) ? $locale : Locales::default();

        $this->tree = $this->loadTree();
    }

    /** Düzenlenen kaydı getirir. */
    public function subject(): Model
    {
        return match ($this->subjectType) {
            'page' => Page::with('translations')->findOrFail($this->subjectId),
            'content' => ContentItem::with('translations', 'type')->findOrFail($this->subjectId),
            default => Template::findOrFail($this->subjectId),
        };
    }

    private function loadTree(): array
    {
        $subject = $this->subject();

        // Şablonlar tüm dillerde ortaktır; sayfa ve içerikte her dilin kendi
        // düzeni vardır.
        $data = $this->subjectType === 'template'
            ? $subject->builder_data
            : $subject->translation($this->locale)?->builder_data;

        return self::plainArray(Tree::fromArray($data)->toArray()['elements']);
    }

    /**
     * Ağacı düzenleyicinin beklediği düz dizi biçimine indirger.
     *
     * `Element::toArray()` ayarları bilinçli olarak nesneye çevirir: boş ayar
     * kümesi JSON'a `[]` değil `{}` olarak yazılsın diye. Düzenleyici ise
     * ayarlara dizi gibi erişir (`$element['settings'][$key]`), dolayısıyla o
     * nesneler burada tekrar diziye indirgenmezse ilk seçimde
     * "Cannot use object of type stdClass as array" hatası alınır.
     *
     * Saklama biçimi değişmiyor; bu dönüşüm yalnızca bellekteki kopya için.
     */
    private static function plainArray(array $value): array
    {
        return json_decode(json_encode($value), true) ?? [];
    }

    // ── Ağaç işlemleri ───────────────────────────────────────────────────

    /** Aynı alanda art arda yapılan düzeltmeleri tek adımda toplamak için. */
    public ?string $lastEdit = null;

    public ?int $lastEditAt = null;

    /**
     * Değişiklikten önceki hâli geri alma yığınına iter.
     *
     * `$coalesceKey` verildiğinde, aynı alanda kısa aralıkla yapılan
     * düzeltmeler tek bir adım sayılır — bir başlığı yazarken her harf için
     * ayrı geri alma adımı oluşmaz.
     */
    private function pushHistory(?string $coalesceKey = null): void
    {
        $now = time();

        if ($coalesceKey !== null
            && $this->lastEdit === $coalesceKey
            && $this->lastEditAt !== null
            && $now - $this->lastEditAt < 3) {
            $this->lastEditAt = $now;
            $this->dirty = true;

            return;
        }

        $this->lastEdit = $coalesceKey;
        $this->lastEditAt = $now;

        $key = $this->historyKey();
        $stack = session()->get($key, []);

        $stack[] = $this->tree;

        // Yığın sınırsız büyürse oturum şişer; son 25 adım yeterlidir.
        if (count($stack) > 25) {
            array_shift($stack);
        }

        session()->put($key, $stack);
        session()->forget($key.':redo');

        $this->dirty = true;
        $this->dispatch('builder-dirty');
    }

    private function historyKey(): string
    {
        return "builder:{$this->subjectType}:{$this->subjectId}:{$this->locale}:history";
    }

    public function undo(): void
    {
        $stack = session()->get($this->historyKey(), []);

        if (! $stack) {
            return;
        }

        $redo = session()->get($this->historyKey().':redo', []);
        $redo[] = $this->tree;
        session()->put($this->historyKey().':redo', $redo);

        $this->tree = array_pop($stack);
        session()->put($this->historyKey(), $stack);

        $this->ensureSelectionExists();
    }

    public function redo(): void
    {
        $redo = session()->get($this->historyKey().':redo', []);

        if (! $redo) {
            return;
        }

        $stack = session()->get($this->historyKey(), []);
        $stack[] = $this->tree;
        session()->put($this->historyKey(), $stack);

        $this->tree = array_pop($redo);
        session()->put($this->historyKey().':redo', $redo);

        $this->ensureSelectionExists();
    }

    public function canUndo(): bool
    {
        return (bool) session()->get($this->historyKey(), []);
    }

    public function canRedo(): bool
    {
        return (bool) session()->get($this->historyKey().':redo', []);
    }

    private function ensureSelectionExists(): void
    {
        if ($this->selectedId && ! TreeEditor::find($this->tree, $this->selectedId)) {
            $this->selectedId = null;
        }
    }

    public function addSection(array $widths, ?string $afterId = null): void
    {
        $this->pushHistory();

        $section = TreeEditor::makeSection($widths);

        if ($afterId && $position = TreeEditor::locate($this->tree, $afterId)) {
            $this->tree = TreeEditor::insert($this->tree, $section, $position['parent'], $position['index'] + 1);
        } else {
            $this->tree[] = $section;
        }

        $this->selectedId = $section['id'];
        $this->settingsTab = 'style';
        $this->showLayoutPicker = false;
        $this->layoutTargetId = null;
    }

    /**
     * Widget ekler. Hedef sütun belirtilmemişse seçili elemanın sütunu,
     * o da yoksa sayfadaki son sütun kullanılır. Kullanıcı bir öğeye
     * tıkladığında "nereye eklenecek" diye sorulmaz.
     */
    public function addWidget(string $key, ?string $columnId = null, ?int $index = null): void
    {
        $registry = app(WidgetRegistry::class);

        if (! $widget = $registry->find($key)) {
            return;
        }

        $columnId ??= $this->defaultColumn();

        if ($columnId === null) {
            // Hiç bölüm yoksa önce tek sütunlu bir bölüm açılır.
            $this->addSection([100]);
            $columnId = $this->tree[array_key_last($this->tree)]['elements'][0]['id'];
        } else {
            $this->pushHistory();
        }

        $element = TreeEditor::makeWidget($key, $widget->starterSettings());
        $this->tree = TreeEditor::insert($this->tree, $element, $columnId, $index);

        $this->selectedId = $element['id'];
        $this->settingsTab = 'content';
    }

    /** Yeni widget'ın düşeceği en makul sütun. */
    private function defaultColumn(): ?string
    {
        if ($this->selectedId) {
            $selected = TreeEditor::find($this->tree, $this->selectedId);

            if (($selected['type'] ?? null) === 'column') {
                return $this->selectedId;
            }

            if ($position = TreeEditor::locate($this->tree, $this->selectedId)) {
                if ($position['parent'] && ($parent = TreeEditor::find($this->tree, $position['parent']))) {
                    if (($parent['type'] ?? null) === 'column') {
                        return $parent['id'];
                    }
                }
            }
        }

        // Sayfadaki son bölümün ilk sütunu
        $last = end($this->tree) ?: null;

        return $last['elements'][0]['id'] ?? null;
    }

    public function selectElement(?string $id): void
    {
        $this->selectedId = $id;
        $this->settingsTab = 'content';
    }

    public function deleteElement(string $id): void
    {
        $this->pushHistory();
        $this->tree = TreeEditor::remove($this->tree, $id);

        if ($this->selectedId === $id) {
            $this->selectedId = null;
        }
    }

    public function duplicateElement(string $id): void
    {
        $this->pushHistory();
        $this->tree = TreeEditor::duplicate($this->tree, $id);
    }

    /** Sürükle-bırak sonrası tarayıcıdan gelir. */
    public function moveElement(string $id, ?string $parentId, int $index): void
    {
        $this->pushHistory();
        $this->tree = TreeEditor::move($this->tree, $id, $parentId, $index);
    }

    /**
     * Seçili elemanın tek bir ayarını değiştirir.
     *
     * Cihaza bağlı ayarlarda değer o cihazın kutusuna yazılır; masaüstünde
     * yapılan değişiklik diğer cihazlara da yansır (kullanıcı isterse
     * tablet/telefon için ayrıca ezer).
     */
    public function setSetting(string $key, mixed $value, bool $responsive = false): void
    {
        if (! $this->selectedId) {
            return;
        }

        $element = TreeEditor::find($this->tree, $this->selectedId);

        if ($element === null) {
            return;
        }

        $this->pushHistory("{$this->selectedId}:{$key}");

        $settings = $element['settings'] ?? [];

        if ($responsive) {
            $current = is_array($settings[$key] ?? null) && $this->isDeviceMap($settings[$key])
                ? $settings[$key]
                : [];
            $current[$this->device] = $value;
            $settings[$key] = $current;
        } else {
            $settings[$key] = $value;
        }

        $this->tree = TreeEditor::updateSettings($this->tree, $this->selectedId, $settings);
    }

    private function isDeviceMap(array $value): bool
    {
        return (bool) array_intersect(array_keys($value), ['d', 't', 'm']);
    }

    /** Bir ayarı geçerli cihaz için okur (ezilmemişse masaüstüne düşer). */
    public function settingValue(string $key, bool $responsive = false, mixed $default = null): mixed
    {
        $element = $this->selectedId ? TreeEditor::find($this->tree, $this->selectedId) : null;
        $value = $element['settings'][$key] ?? null;

        if ($value === null) {
            return $default;
        }

        if (! $responsive || ! is_array($value) || ! $this->isDeviceMap($value)) {
            return $responsive && ! is_array($value) ? $value : ($responsive ? $default : $value);
        }

        return $value[$this->device] ?? $value['d'] ?? $default;
    }

    // ── Tekrarlayan listeler ─────────────────────────────────────────────
    //
    // Slayt, SSS maddesi, ekip üyesi gibi listeler burada yönetilir.
    // İşlemler bilinçli olarak sunucu tarafında: alternatifi, her düğmeye
    // listenin tamamının bir kopyasını gömmekti ve sayfa satır sayısının
    // karesiyle büyüyordu.

    /** Listenin geçerli hâlini döndürür. */
    private function repeaterRows(string $key): array
    {
        $element = $this->selectedId ? TreeEditor::find($this->tree, $this->selectedId) : null;
        // Hiç dokunulmamış liste panelde varsayılan satırlarıyla görünür;
        // ilk düzenleme de o satırların üzerinden yapılmalı, yoksa satır
        // eklemek ya da silmek varsayılanları sessizce yok eder.
        $rows = $element['settings'][$key] ?? ($this->controlDefinition($key)['default'] ?? null);

        return is_array($rows) ? array_values($rows) : [];
    }

    private function writeRepeater(string $key, array $rows, ?string $coalesceKey = null): void
    {
        if (! $this->selectedId) {
            return;
        }

        $element = TreeEditor::find($this->tree, $this->selectedId);

        if ($element === null) {
            return;
        }

        $this->pushHistory($coalesceKey);

        $settings = $element['settings'] ?? [];
        $settings[$key] = array_values($rows);

        $this->tree = TreeEditor::updateSettings($this->tree, $this->selectedId, $settings);
    }

    /** Listeye, tanımdaki varsayılanlarla dolu yeni bir satır ekler. */
    public function addRepeaterRow(string $key): void
    {
        $control = $this->controlDefinition($key);

        if ($control === null) {
            return;
        }

        $blank = [];

        foreach ($control['fields'] ?? [] as $fieldKey => $field) {
            $blank[$fieldKey] = $field['default'] ?? '';
        }

        $this->writeRepeater($key, [...$this->repeaterRows($key), $blank]);
    }

    public function removeRepeaterRow(string $key, int $index): void
    {
        $rows = $this->repeaterRows($key);

        if (! array_key_exists($index, $rows)) {
            return;
        }

        unset($rows[$index]);
        $this->writeRepeater($key, $rows);
    }

    public function moveRepeaterRow(string $key, int $index, string $direction): void
    {
        $rows = $this->repeaterRows($key);
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if (! isset($rows[$index], $rows[$target])) {
            return;
        }

        [$rows[$index], $rows[$target]] = [$rows[$target], $rows[$index]];

        $this->writeRepeater($key, $rows);
    }

    /** Bir satırdaki tek alanı günceller. */
    public function setRepeaterField(string $key, int $index, string $field, mixed $value): void
    {
        $rows = $this->repeaterRows($key);

        if (! array_key_exists($index, $rows)) {
            return;
        }

        $rows[$index][$field] = $value;

        // Aynı alanda art arda yazılan harfler tek geri alma adımı sayılır.
        $this->writeRepeater($key, $rows, "{$this->selectedId}:{$key}:{$index}:{$field}");
    }

    /** Seçili elemanın denetim tanımını anahtarına göre bulur. */
    private function controlDefinition(string $key): ?array
    {
        $element = $this->selectedId ? TreeEditor::find($this->tree, $this->selectedId) : null;

        if ($element === null) {
            return null;
        }

        $controls = $this->controlsFor($element, app(WidgetRegistry::class));

        foreach ($controls as $tab) {
            if (isset($tab['controls'][$key])) {
                return $tab['controls'][$key];
            }
        }

        return null;
    }

    // ── Kaydetme ─────────────────────────────────────────────────────────

    public function save(): void
    {
        $tree = Tree::fromArray(['elements' => $this->tree]);
        $css = app(StyleCompiler::class)->compile($tree);
        $subject = $this->subject();

        if ($this->subjectType === 'template') {
            $subject->update(['builder_data' => $tree->toArray(), 'compiled_css' => $css]);
        } else {
            // Kaydetmeden önceki hâl sürüm olarak saklanır; müşteri
            // beğenmezse tek tıkla geri dönebilir.
            if ($this->subjectType === 'page') {
                $previous = $subject->translation($this->locale)?->builder_data;

                if ($previous) {
                    PageRevision::create([
                        'page_id' => $subject->getKey(),
                        'locale' => $this->locale,
                        'user_id' => auth()->id(),
                        'builder_data' => $previous,
                    ]);

                    // Sürüm tablosu sınırsız büyümesin.
                    $keep = PageRevision::where('page_id', $subject->getKey())
                        ->where('locale', $this->locale)
                        ->latest('created_at')->limit(20)->pluck('id');

                    PageRevision::where('page_id', $subject->getKey())
                        ->where('locale', $this->locale)
                        ->whereNotIn('id', $keep)->delete();
                }
            }

            $subject->fillTranslation($this->locale, [
                'builder_data' => $tree->toArray(),
                'compiled_css' => $css,
            ]);
        }

        ActivityLogger::log('builder.save', $subject, 'Tasarım kaydedildi: '.$this->subjectTitle());

        session()->forget([$this->historyKey(), $this->historyKey().':redo']);

        $this->dirty = false;
        $this->dispatch('builder-clean');
        $this->dispatch('notify', message: 'Değişiklikler kaydedildi.');
    }

    /** Seçili bölümü kütüphaneye kaydeder. */
    public function saveAsTemplate(string $id, string $name): void
    {
        $element = TreeEditor::find($this->tree, $id);

        if ($element === null || blank($name)) {
            return;
        }

        Template::create([
            'name' => $name,
            'type' => 'section',
            'builder_data' => ['version' => 1, 'elements' => [$element]],
            'author_id' => auth()->id(),
        ]);

        $this->dispatch('notify', message: 'Bölüm kütüphaneye kaydedildi.');
    }

    /** Kütüphanedeki bir bölümü sayfaya ekler. */
    public function insertTemplate(int $templateId): void
    {
        $template = Template::find($templateId);

        if (! $template) {
            return;
        }

        $this->pushHistory();

        foreach (Tree::fromArray($template->builder_data)->elements as $element) {
            $this->tree[] = self::plainArray($element->withFreshIds()->toArray());
        }
    }

    public function switchLocale(string $locale): void
    {
        if (! Locales::exists($locale) || $this->subjectType === 'template') {
            return;
        }

        $this->locale = $locale;
        $this->selectedId = null;
        $this->tree = $this->loadTree();
        $this->dirty = false;
    }

    /** Diğer dildeki düzeni bu dile kopyalar. */
    public function copyFromLocale(string $from): void
    {
        $subject = $this->subject();
        $data = $subject->translation($from)?->builder_data;

        if (! $data) {
            $this->dispatch('notify', message: 'Kopyalanacak bir tasarım bulunamadı.');

            return;
        }

        $this->pushHistory();

        // Kimlikler yenilenir; iki dil birbirinin stilini ezmez.
        $this->tree = self::plainArray(array_map(
            fn ($element) => \App\Builder\Element::fromArray($element)->withFreshIds()->toArray(),
            Tree::fromArray($data)->toArray()['elements']
        ));

        $this->dispatch('notify', message: 'Tasarım kopyalandı. Metinleri bu dile çevirmeyi unutmayın.');
    }

    private function subjectTitle(): string
    {
        $subject = $this->subject();

        return $this->subjectType === 'template'
            ? $subject->name
            : ((string) $subject->t('title', $this->locale) ?: 'Adsız');
    }

    // ── Çizim ────────────────────────────────────────────────────────────

    public function render()
    {
        $registry = app(WidgetRegistry::class);

        $selected = $this->selectedId ? TreeEditor::find($this->tree, $this->selectedId) : null;

        return view('livewire.builder.editor', [
            'previewUrl' => $this->previewUrl(),
            'widgets' => $this->filteredWidgets($registry),
            'selected' => $selected,
            'selectedControls' => $this->controlsFor($selected, $registry),
            'selectedLabel' => $this->labelFor($selected, $registry),
            'layouts' => TreeEditor::LAYOUTS,
            'locales' => Locales::all(),
            'templates' => Template::where('type', 'section')->latest()->limit(30)->get(),
            'subjectTitle' => $this->subjectTitle(),
            'canUndo' => $this->canUndo(),
            'canRedo' => $this->canRedo(),
        ])->title('Tasarım — '.$this->subjectTitle());
    }

    /**
     * Ortadaki önizleme çerçevesinin adresi.
     *
     * Anahtar ağacın içeriğinden türetilir: değişmeyen ağaç (ör. yalnızca
     * seçim değiştiğinde) çerçeveyi yeniden yüklemez, geri alınan değişiklik
     * önbellekteki eski önizlemeye döner.
     */
    private function previewUrl(): string
    {
        $payload = [
            'user' => auth()->id(),
            'type' => $this->subjectType,
            'id' => $this->subjectId,
            'locale' => $this->locale,
            'tree' => $this->tree,
        ];

        $key = sha1(json_encode($payload));
        $cacheKey = BuilderPreviewController::cacheKey($key);

        if (! Cache::has($cacheKey)) {
            Cache::put($cacheKey, $payload, BuilderPreviewController::TTL);
        }

        return route('admin.builder.preview', $key);
    }

    private function filteredWidgets(WidgetRegistry $registry)
    {
        $groups = $registry->grouped();

        if (blank($this->widgetSearch)) {
            return $groups;
        }

        $needle = mb_strtolower($this->widgetSearch);

        return $groups
            ->map(fn ($items) => $items->filter(
                fn ($item) => str_contains(mb_strtolower($item['label']), $needle)
            )->values())
            ->filter(fn ($items) => $items->isNotEmpty());
    }

    /** Seçili elemanın ayar panelinde gösterilecek sekmeler ve denetimler. */
    private function controlsFor(?array $element, WidgetRegistry $registry): array
    {
        if ($element === null) {
            return [];
        }

        $tabs = [];

        if (($element['type'] ?? null) === 'widget') {
            $widget = $registry->find($element['widget'] ?? null);
            $tabs = $widget?->controls() ?? [];
        } elseif ($element['type'] === 'section') {
            $tabs['content'] = ['label' => 'Düzen', 'controls' => Controls::section()];
        } elseif ($element['type'] === 'column') {
            $tabs['content'] = ['label' => 'Düzen', 'controls' => Controls::column()];
        }

        // Ortak ayarlar her elemanda "Gelişmiş" sekmesinde toplanır.
        $tabs['advanced'] = ['label' => 'Gelişmiş', 'controls' => Controls::common($element['type'])];

        return $tabs;
    }

    private function labelFor(?array $element, WidgetRegistry $registry): string
    {
        return match ($element['type'] ?? null) {
            'section' => 'Bölüm',
            'column' => 'Sütun',
            'widget' => $registry->find($element['widget'] ?? null)?->label() ?? 'Öğe',
            default => '',
        };
    }
}
