<?php

namespace App\Livewire\Admin\Design;

use App\Models\ContentItem;
use App\Models\ContentType;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Services\ActivityLogger;
use App\Support\Locales;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Menü düzenleyici.
 *
 * Kullanıcı soldan bir sayfa/içerik seçip menüye ekler, sağda sıralar ve
 * iç içe geçirir. Bağlantı adresi el ile yazılmaz; sayfanın adresi
 * değiştiğinde menü kendiliğinden doğru kalır.
 */
#[Layout('layouts.admin')]
class MenuBuilder extends Component
{
    public ?int $menuId = null;

    public bool $creatingMenu = false;

    public string $newMenuName = '';

    public string $newMenuKey = '';

    public ?int $editingItemId = null;

    public array $itemForm = [];

    public function mount(): void
    {
        $this->menuId = Menu::orderBy('id')->value('id');
    }

    public function createMenu(): void
    {
        $this->validate([
            'newMenuName' => 'required|string|max:80',
            'newMenuKey' => 'required|alpha_dash|max:40|unique:menus,key',
        ], [
            'newMenuName.required' => 'Menüye bir ad verin.',
            'newMenuKey.unique' => 'Bu anahtar zaten kullanılıyor.',
            'newMenuKey.alpha_dash' => 'Anahtar yalnızca harf, rakam ve tire içerebilir.',
        ]);

        $menu = Menu::create(['name' => $this->newMenuName, 'key' => $this->newMenuKey]);
        ActivityLogger::created($menu, "Menü oluşturuldu: {$menu->name}");

        $this->menuId = $menu->id;
        $this->reset('newMenuName', 'newMenuKey', 'creatingMenu');
    }

    /** Sol taraftaki listeden menüye ekler. */
    public function addItem(string $type, ?int $targetId = null, ?string $label = null): void
    {
        if (! $this->menuId) {
            return;
        }

        $item = MenuItem::create([
            'menu_id' => $this->menuId,
            'type' => $type,
            'target_id' => $targetId,
            'label' => Locales::map($label ?? 'Yeni bağlantı'),
            'sort' => (MenuItem::where('menu_id', $this->menuId)->max('sort') ?? 0) + 1,
        ]);

        // Diğer dillerdeki etiketi de hedefin o dildeki başlığıyla doldur.
        if ($type === 'page' && $page = Page::withTranslations()->find($targetId)) {
            $labels = [];

            foreach (Locales::codes() as $code) {
                $labels[$code] = (string) ($page->t('title', $code) ?: $label);
            }

            $item->update(['label' => $labels]);
        }

        $this->dispatch('notify', message: 'Menüye eklendi.');
    }

    public function editItem(int $id): void
    {
        $item = MenuItem::findOrFail($id);

        $this->editingItemId = $id;
        $this->itemForm = [
            'label' => array_replace(Locales::map(''), $item->label ?? []),
            'url' => $item->url ?? '',
            'target' => $item->target,
            'parent_id' => $item->parent_id,
            'is_active' => $item->is_active,
        ];
    }

    public function saveItem(): void
    {
        $item = MenuItem::findOrFail($this->editingItemId);

        $rules = ['itemForm.url' => 'nullable|string|max:500'];
        $rules['itemForm.label.'.Locales::default()] = 'required|string|max:120';

        $this->validate($rules, [
            'itemForm.label.'.Locales::default().'.required' => 'Menüde görünecek yazıyı girin.',
        ]);

        // Öğe kendi altına taşınamaz.
        $parentId = $this->itemForm['parent_id'] ?: null;

        if ($parentId && $this->isDescendant($item, (int) $parentId)) {
            $this->addError('itemForm.parent_id', 'Bir öğe kendi alt öğesinin altına taşınamaz.');

            return;
        }

        $item->update([
            'label' => $this->itemForm['label'],
            'url' => $this->itemForm['url'] ?: null,
            'target' => $this->itemForm['target'],
            'parent_id' => $parentId,
            'is_active' => (bool) $this->itemForm['is_active'],
        ]);

        ActivityLogger::updated($item, 'Menü öğesi güncellendi: '.$item->title());

        $this->reset('editingItemId', 'itemForm');
        $this->dispatch('notify', message: 'Kaydedildi.');
    }

    private function isDescendant(MenuItem $item, int $candidateParentId): bool
    {
        $node = MenuItem::find($candidateParentId);
        $guard = 0;

        while ($node && $guard++ < 10) {
            if ($node->id === $item->id) {
                return true;
            }

            $node = $node->parent_id ? MenuItem::find($node->parent_id) : null;
        }

        return false;
    }

    public function moveItem(int $id, string $direction): void
    {
        $item = MenuItem::findOrFail($id);

        $neighbour = MenuItem::where('menu_id', $item->menu_id)
            ->where('parent_id', $item->parent_id)
            ->when($direction === 'up',
                fn ($q) => $q->where('sort', '<', $item->sort)->orderByDesc('sort'),
                fn ($q) => $q->where('sort', '>', $item->sort)->orderBy('sort'))
            ->first();

        if (! $neighbour) {
            return;
        }

        [$item->sort, $neighbour->sort] = [$neighbour->sort, $item->sort];
        $item->save();
        $neighbour->save();
    }

    /** Sürükle-bırak sonrasında bütün ağacın sırasını tek işlemde kaydeder. */
    public function reorderItems(array $positions): void
    {
        if (! $this->menuId) {
            return;
        }

        $menu = Menu::with('items')->findOrFail($this->menuId);
        $validIds = $menu->items->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();
        $receivedIds = collect($positions)->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();

        // Eksik/fazla kimlik içeren istemci verisini asla kısmen uygulama.
        if ($receivedIds !== $validIds) {
            $this->dispatch('notify', message: 'Menü sırası kaydedilemedi. Sayfayı yenileyip tekrar deneyin.');

            return;
        }

        $parents = [];

        foreach ($positions as $position) {
            $id = (int) ($position['id'] ?? 0);
            $parentId = filled($position['parent_id'] ?? null) ? (int) $position['parent_id'] : null;

            if ($parentId !== null && (! in_array($parentId, $validIds, true) || $parentId === $id)) {
                $this->dispatch('notify', message: 'Bir menü öğesi kendi altına taşınamaz.');

                return;
            }

            $parents[$id] = $parentId;
        }

        // Döngüleri ve kurumsal menülerde kullanılabilirliği bozan aşırı derinliği engelle.
        foreach ($validIds as $id) {
            $seen = [$id];
            $cursor = $parents[$id] ?? null;
            $depth = 0;

            while ($cursor !== null) {
                if (in_array($cursor, $seen, true)) {
                    $this->dispatch('notify', message: 'Bir menü öğesi kendi alt öğesinin altına taşınamaz.');

                    return;
                }

                $seen[] = $cursor;
                $cursor = $parents[$cursor] ?? null;

                if (++$depth > 3) {
                    $this->dispatch('notify', message: 'Menü en fazla üç alt seviyeden oluşabilir.');

                    return;
                }
            }
        }

        DB::transaction(function () use ($positions, $menu): void {
            foreach ($positions as $position) {
                $menu->items->firstWhere('id', (int) $position['id'])?->update([
                    'parent_id' => filled($position['parent_id'] ?? null) ? (int) $position['parent_id'] : null,
                    'sort' => max(0, (int) ($position['sort'] ?? 0)),
                ]);
            }
        });

        ActivityLogger::updated($menu, "Menü sıralandı: {$menu->name}");
        $this->dispatch('notify', message: 'Menü sırası kaydedildi.');
    }

    public function deleteItem(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        ActivityLogger::deleted($item, 'Menü öğesi silindi: '.$item->title());
        $item->delete();
    }

    public function render()
    {
        $menu = $this->menuId ? Menu::with('items')->find($this->menuId) : null;

        return view('livewire.admin.design.menu-builder', [
            'menus' => Menu::orderBy('id')->get(),
            'menu' => $menu,
            'rootItems' => $menu
                ? $menu->items->whereNull('parent_id')->sortBy('sort')
                : collect(),
            'allItems' => $menu ? $menu->items : collect(),
            'pages' => Page::withTranslations()->orderBy('sort')->get(),
            'contentTypes' => ContentType::where('is_active', true)->orderBy('sort')->get(),
            'recentContent' => ContentItem::withTranslations()->with('type')->latest()->limit(30)->get(),
            'locales' => Locales::all(),
        ])->title('Menüler');
    }
}
