<div class="space-y-4" data-menu-builder x-data x-init="$nextTick(() => window.initMenuSortables($el))">

    <div class="flex flex-wrap items-center gap-2">
        @if ($menus->isNotEmpty())
            <select wire:model.live="menuId" class="field w-auto" aria-label="Menü seçin">
                @foreach ($menus as $m)
                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                @endforeach
            </select>
        @endif

        <button wire:click="$set('creatingMenu', true)" class="btn-secondary">
            <x-icon name="plus" class="size-4"/> Yeni menü
        </button>

        @if ($menu)
            <p class="ml-auto text-xs text-ink-500">
                Bu menüyü sayfaya yerleştirmek için kurucudaki <strong>Menü</strong> öğesini kullanın
                ve anahtar olarak <code class="rounded bg-ink-100 px-1 font-mono">{{ $menu->key }}</code> seçin.
            </p>
        @endif
    </div>

    @if ($creatingMenu)
        <form wire:submit="createMenu" class="card flex flex-wrap items-start gap-3 p-4">
            <div class="min-w-48 flex-1">
                <label for="m-name" class="label">Menü adı</label>
                <input id="m-name" type="text" wire:model="newMenuName"
                       class="field @error('newMenuName') field-error @enderror" placeholder="Üst menü">
                @error('newMenuName') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="min-w-40 flex-1">
                <label for="m-key" class="label">Anahtar</label>
                <input id="m-key" type="text" wire:model="newMenuKey"
                       class="field font-mono text-xs @error('newMenuKey') field-error @enderror" placeholder="header">
                @error('newMenuKey') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 pt-6.5">
                <button type="submit" class="btn-primary">Oluştur</button>
                <button type="button" wire:click="$set('creatingMenu', false)" class="btn-ghost">Vazgeç</button>
            </div>
        </form>
    @endif

    @if (! $menu)
        <div class="card flex flex-col items-center justify-center py-16 text-center">
            <x-icon name="menu" class="size-10 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">Henüz menü yok.</p>
            <p class="mt-1 max-w-sm text-sm text-ink-400">Sitenin üst ve alt bölümlerinde görünecek bağlantıları burada düzenlersiniz.</p>
        </div>
    @else
        <div class="grid gap-4 lg:grid-cols-[20rem_1fr]">

            {{-- Eklenebilecekler --}}
            <div class="space-y-3">
                <section class="card overflow-hidden">
                    <h2 class="border-b border-ink-100 px-4 py-2.5 text-sm font-semibold text-ink-900">Sayfalar</h2>
                    <div class="max-h-64 overflow-y-auto p-2">
                        @forelse ($pages as $page)
                            <button wire:click="addItem('page', {{ $page->id }}, @js($page->t('title') ?: 'Sayfa'))"
                                    class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-left text-sm text-ink-700 transition hover:bg-brand-50 hover:text-brand-700">
                                <x-icon name="plus" class="size-3.5 shrink-0 text-ink-400"/>
                                <span class="min-w-0 truncate">{{ $page->t('title') ?: 'Adsız' }}</span>
                            </button>
                        @empty
                            <p class="px-2 py-4 text-center text-xs text-ink-400">Sayfa yok.</p>
                        @endforelse
                    </div>
                </section>

                <section class="card overflow-hidden">
                    <h2 class="border-b border-ink-100 px-4 py-2.5 text-sm font-semibold text-ink-900">Liste sayfaları</h2>
                    <div class="p-2">
                        <button wire:click="addItem('home', null, 'Ana Sayfa')"
                                class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-left text-sm text-ink-700 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="plus" class="size-3.5 shrink-0 text-ink-400"/> Ana Sayfa
                        </button>
                        @foreach ($contentTypes as $ct)
                            @if ($ct->has_archive)
                                <button wire:click="addItem('archive', {{ $ct->id }}, @js($ct->pluralLabel('tr')))"
                                        class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-left text-sm text-ink-700 transition hover:bg-brand-50 hover:text-brand-700">
                                    <x-icon name="plus" class="size-3.5 shrink-0 text-ink-400"/>
                                    <span class="min-w-0 truncate">{{ $ct->pluralLabel('tr') }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </section>

                <section class="card overflow-hidden">
                    <h2 class="border-b border-ink-100 px-4 py-2.5 text-sm font-semibold text-ink-900">Özel bağlantı</h2>
                    <div class="p-2">
                        <button wire:click="addItem('url', null, 'Yeni bağlantı')"
                                class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-left text-sm text-ink-700 transition hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="plus" class="size-3.5 shrink-0 text-ink-400"/> Adres gireceğim
                        </button>
                    </div>
                </section>
            </div>

            {{-- Menü yapısı --}}
            <section class="card overflow-hidden">
                <div class="border-b border-ink-100 px-4 py-2.5">
                    <h2 class="text-sm font-semibold text-ink-900">{{ $menu->name }}</h2>
                    <p class="mt-0.5 text-xs text-ink-500">
                        Noktalı tutamaçtan sürükleyin. Oklarla da sıralayabilir veya alt menüye alabilirsiniz.
                    </p>
                </div>

                @if ($rootItems->isEmpty())
                    <p class="px-4 py-14 text-center text-sm text-ink-400">
                        Menü boş. Soldaki listelerden bağlantı ekleyin.
                    </p>
                @else
                    <ol data-menu-sortable data-parent-id="" class="space-y-2 bg-ink-50/60 p-3">
                        @include('livewire.admin.design.partials.menu-items', [
                            'items' => $rootItems,
                            'allItems' => $allItems,
                            'depth' => 0,
                        ])
                    </ol>
                @endif
            </section>
        </div>
    @endif

    {{-- Öğe düzenleme --}}
    @if ($editingItemId)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/50 p-4"
             wire:click.self="$set('editingItemId', null)" role="dialog" aria-modal="true" aria-label="Menü öğesi">
            <form wire:submit="saveItem" class="w-full max-w-md rounded-xl bg-white shadow-xl">
                <h2 class="border-b border-ink-100 px-5 py-3.5 text-sm font-semibold text-ink-900">Menü öğesi</h2>

                <div class="space-y-4 p-5">
                    @foreach ($locales as $locale)
                        <div>
                            <label for="mi-{{ $locale->code }}" class="label">
                                Menüde görünecek yazı
                                @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->native_name }})</span> @endif
                            </label>
                            <input id="mi-{{ $locale->code }}" type="text" wire:model="itemForm.label.{{ $locale->code }}"
                                   class="field @error('itemForm.label.'.$locale->code) field-error @enderror">
                            @error('itemForm.label.'.$locale->code) <p class="error">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    @if (\App\Models\MenuItem::find($editingItemId)?->type === 'url')
                        <div>
                            <label for="mi-url" class="label">Adres</label>
                            <input id="mi-url" type="text" wire:model="itemForm.url" class="field" placeholder="https://…">
                        </div>
                    @endif

                    <div>
                        <label for="mi-parent" class="label">Üst öğe</label>
                        <select id="mi-parent" wire:model="itemForm.parent_id"
                                class="field @error('itemForm.parent_id') field-error @enderror">
                            <option value="">— Yok (ana seviye) —</option>
                            @foreach ($allItems->whereNull('parent_id')->where('id', '!=', $editingItemId) as $option)
                                <option value="{{ $option->id }}">{{ $option->title() }}</option>
                            @endforeach
                        </select>
                        @error('itemForm.parent_id') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="mi-target" class="label">Açılma biçimi</label>
                        <select id="mi-target" wire:model="itemForm.target" class="field">
                            <option value="_self">Aynı sekmede</option>
                            <option value="_blank">Yeni sekmede</option>
                        </select>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" wire:model="itemForm.is_active" class="size-4 rounded border-ink-300 text-brand-600">
                        Menüde görünsün
                    </label>
                </div>

                <div class="flex justify-end gap-2 border-t border-ink-100 px-5 py-3">
                    <button type="button" wire:click="$set('editingItemId', null)" class="btn-secondary">Vazgeç</button>
                    <button type="submit" class="btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    @endif
</div>
