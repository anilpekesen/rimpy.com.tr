<div class="flex h-screen flex-col" data-builder-canvas
     @shortcut-save.window="$wire.save()"
     @shortcut-undo.window="$wire.undo()"
     @shortcut-redo.window="$wire.redo()">

    {{-- ══ Üst çubuk ══════════════════════════════════════════════════════ --}}
    <header class="flex h-13 shrink-0 items-center gap-2 border-b border-ink-300 bg-white px-3">
        <a href="{{ $subjectType === 'page' ? route('admin.pages.index') : route('admin.templates.index') }}"
           class="btn-ghost btn-sm shrink-0" title="Panele dön">
            <x-icon name="chevron-right" class="size-4 rotate-180"/>
            <span class="hidden sm:inline">Çık</span>
        </a>

        <div class="min-w-0 border-l border-ink-200 pl-3">
            <p class="truncate text-sm font-medium text-ink-900">{{ $subjectTitle }}</p>
            <p class="text-[11px] text-ink-400">
                {{ $dirty ? 'Kaydedilmemiş değişiklik var' : 'Tüm değişiklikler kaydedildi' }}
            </p>
        </div>

        {{-- Dil --}}
        @if ($locales->count() > 1 && $subjectType !== 'template')
            <div class="ml-2 flex shrink-0 rounded-lg border border-ink-200 p-0.5">
                @foreach ($locales as $lang)
                    <button wire:click="switchLocale('{{ $lang->code }}')"
                            class="rounded-md px-2 py-1 text-xs font-semibold uppercase transition
                                   {{ $locale === $lang->code ? 'bg-ink-900 text-white' : 'text-ink-500 hover:bg-ink-100' }}">
                        {{ $lang->code }}
                    </button>
                @endforeach
            </div>
        @endif

        {{-- Cihaz önizleme --}}
        <div class="mx-auto flex shrink-0 rounded-lg border border-ink-200 p-0.5">
            @foreach (['d' => ['Masaüstü', 'M'], 't' => ['Tablet', 'T'], 'm' => ['Telefon', 'C']] as $key => [$label, $short])
                <button wire:click="$set('device', '{{ $key }}')" title="{{ $label }}"
                        class="rounded-md px-3 py-1 text-xs font-medium transition
                               {{ $device === $key ? 'bg-ink-900 text-white' : 'text-ink-500 hover:bg-ink-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="flex shrink-0 items-center gap-1">
            <button wire:click="undo" @disabled(! $canUndo) class="btn-ghost btn-sm" title="Geri al (⌘Z)">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/>
                </svg>
                <span class="sr-only">Geri al</span>
            </button>
            <button wire:click="redo" @disabled(! $canRedo) class="btn-ghost btn-sm" title="Yinele (⌘⇧Z)">
                <svg class="size-4 -scale-x-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"/>
                </svg>
                <span class="sr-only">Yinele</span>
            </button>

            @if ($subjectType === 'page')
                <a href="{{ \App\Models\Page::find($subjectId)?->path($locale) }}" target="_blank" rel="noopener"
                   class="btn-ghost btn-sm" title="Sitede önizle">
                    <x-icon name="eye" class="size-4"/><span class="sr-only">Önizle</span>
                </a>
            @endif

            <button wire:click="save" class="btn-primary btn-sm ml-1">
                <span wire:loading.remove wire:target="save">Kaydet</span>
                <span wire:loading wire:target="save">Kaydediliyor…</span>
            </button>
        </div>
    </header>

    <div class="flex min-h-0 flex-1">

        {{-- ══ Sol panel ══════════════════════════════════════════════════ --}}
        <aside class="flex w-64 shrink-0 flex-col border-r border-ink-300 bg-white">
            <div class="flex shrink-0 border-b border-ink-200">
                @foreach (['widgets' => 'Öğeler', 'layers' => 'Katmanlar', 'library' => 'Kütüphane'] as $tab => $label)
                    <button wire:click="$set('leftTab', '{{ $tab }}')"
                            class="flex-1 border-b-2 px-2 py-2.5 text-xs font-medium transition
                                   {{ $leftTab === $tab ? 'border-brand-600 text-brand-700' : 'border-transparent text-ink-500 hover:text-ink-800' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto">
                @if ($leftTab === 'widgets')
                    <div class="sticky top-0 z-10 border-b border-ink-100 bg-white p-2">
                        <input type="search" wire:model.live.debounce.200ms="widgetSearch"
                               placeholder="Öğe ara" class="field text-xs" aria-label="Öğe ara">
                    </div>

                    <div class="p-2">
                        <button wire:click="$set('showLayoutPicker', true)"
                                class="mb-3 flex w-full items-center justify-center gap-1.5 rounded-lg border border-dashed border-ink-300 py-2.5 text-xs font-medium text-ink-600 transition hover:border-brand-400 hover:bg-brand-50 hover:text-brand-700">
                            <x-icon name="plus" class="size-4"/> Yeni bölüm ekle
                        </button>

                        @forelse ($widgets as $category => $items)
                            <p class="px-1 pt-2 pb-1.5 text-[11px] font-semibold tracking-wide text-ink-400 uppercase">
                                {{ \App\Builder\WidgetRegistry::CATEGORIES[$category] ?? $category }}
                            </p>
                            <div class="grid grid-cols-2 gap-1.5">
                                @foreach ($items as $item)
                                    <button wire:click="addWidget('{{ $item['key'] }}')"
                                            class="flex flex-col items-center gap-1.5 rounded-lg border border-ink-200 px-2 py-2.5 text-center transition hover:border-brand-400 hover:bg-brand-50">
                                        <x-icon name="blocks" class="size-4 text-ink-400"/>
                                        <span class="text-[11px] leading-tight text-ink-700">{{ $item['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @empty
                            <p class="px-1 py-8 text-center text-xs text-ink-400">Eşleşen öğe yok.</p>
                        @endforelse
                    </div>

                @elseif ($leftTab === 'layers')
                    <div class="p-2">
                        @include('livewire.builder.partials.layers', ['nodes' => $tree, 'depth' => 0])
                        @if (! $tree)
                            <p class="px-1 py-8 text-center text-xs text-ink-400">Sayfa henüz boş.</p>
                        @endif
                    </div>

                @else
                    <div class="space-y-1.5 p-2">
                        <p class="px-1 pb-1 text-[11px] text-ink-500">
                            Kaydettiğiniz bölümler. Ekleyip düzenleyebilirsiniz.
                        </p>
                        @forelse ($templates as $template)
                            <button wire:click="insertTemplate({{ $template->id }})"
                                    class="flex w-full items-center gap-2 rounded-lg border border-ink-200 px-2.5 py-2 text-left transition hover:border-brand-400 hover:bg-brand-50">
                                <x-icon name="blocks" class="size-4 shrink-0 text-ink-400"/>
                                <span class="min-w-0 flex-1 truncate text-xs text-ink-700">{{ $template->name }}</span>
                                <x-icon name="plus" class="size-3.5 shrink-0 text-ink-400"/>
                            </button>
                        @empty
                            <p class="px-1 py-8 text-center text-xs text-ink-400">
                                Kütüphane boş. Tuvaldeki bir bölümün üzerine gelip “Kütüphaneye kaydet” diyerek buraya ekleyebilirsiniz.
                            </p>
                        @endforelse
                    </div>
                @endif
            </div>
        </aside>

        {{-- ══ Önizleme ════════════════════════════════════════════════════
             Sayfa, sitenin kendi stilleri ve betikleriyle ayrı bir çerçevede
             çizilir. Her değişiklikte yeni sürüm arkadaki ikinci çerçevede
             yüklenir, hazır olunca öne alınır; ekran boşalıp titremez. --}}
        <main class="relative min-w-0 flex-1 bg-ink-200 p-3"
              @click.self="$wire.selectElement(null)"
              x-data="builderPreview">

            <span x-ref="probe" class="hidden" data-url="{{ $previewUrl }}" data-selected="{{ $selectedId }}"></span>

            <div class="relative mx-auto h-full overflow-hidden rounded-md bg-white shadow-lg transition-[max-width] duration-300"
                 style="max-width: {{ ['d' => '100%', 't' => '1024px', 'm' => '390px'][$device] }}">
                <div class="absolute inset-0" wire:ignore>
                    <iframe x-ref="a" title="Sayfa önizlemesi" class="absolute inset-0 size-full border-0 bg-white"></iframe>
                    <iframe x-ref="b" title="Sayfa önizlemesi" class="invisible absolute inset-0 size-full border-0 bg-white" aria-hidden="true"></iframe>
                </div>

                <div x-show="loading" x-transition.opacity
                     class="pointer-events-none absolute right-3 top-3 rounded-full bg-ink-900/85 px-3 py-1.5 text-[11px] font-medium text-white shadow">
                    Önizleme güncelleniyor…
                </div>

                @if (! $tree)
                    <div class="absolute inset-x-0 top-1/3 mx-auto flex max-w-sm flex-col items-center gap-3 rounded-xl bg-white p-6 text-center shadow-lg">
                        <x-icon name="blocks" class="size-10 text-ink-200"/>
                        <div>
                            <p class="text-sm font-medium text-ink-700">Sayfa henüz boş</p>
                            <p class="mt-1 text-sm text-ink-400">Bir bölüm ekleyip içine öğe yerleştirerek başlayın.</p>
                        </div>
                        <button wire:click="$set('showLayoutPicker', true)" class="btn-primary btn-sm">
                            <x-icon name="plus" class="size-4"/> Bölüm ekle
                        </button>
                    </div>
                @endif
            </div>
        </main>

        {{-- ══ Sağ panel ══════════════════════════════════════════════════ --}}
        <aside class="flex w-80 shrink-0 flex-col border-l border-ink-300 bg-white">
            @if (! $selected)
                <div class="flex flex-1 flex-col items-center justify-center gap-2 p-6 text-center">
                    <x-icon name="edit" class="size-8 text-ink-200"/>
                    <p class="text-sm font-medium text-ink-700">Bir öğe seçin</p>
                    <p class="text-xs text-ink-400">
                        Tuvalde düzenlemek istediğiniz başlığa, görsele ya da bölüme tıklayın.
                    </p>
                </div>
            @else
                <div class="flex shrink-0 items-center gap-2 border-b border-ink-200 px-3 py-2.5">
                    <span class="badge-gray">{{ $selectedLabel }}</span>
                    <div class="ml-auto flex items-center gap-0.5">
                        <button wire:click="duplicateElement('{{ $selected['id'] }}')" class="btn-ghost btn-sm" title="Kopyala">
                            <x-icon name="blocks" class="size-3.5"/><span class="sr-only">Kopyala</span>
                        </button>
                        @if ($selected['type'] === 'section')
                            <button x-data
                                    @click="const n = prompt('Bu bölüm kütüphaneye hangi adla kaydedilsin?'); if (n) $wire.saveAsTemplate('{{ $selected['id'] }}', n)"
                                    class="btn-ghost btn-sm" title="Kütüphaneye kaydet">
                                <x-icon name="plus" class="size-3.5"/><span class="sr-only">Kütüphaneye kaydet</span>
                            </button>
                        @endif
                        <button wire:click="deleteElement('{{ $selected['id'] }}')"
                                wire:confirm="Bu öğe ve içindekiler silinecek. Devam edilsin mi?"
                                class="btn-ghost btn-sm text-red-600 hover:bg-red-50" title="Sil">
                            <x-icon name="trash" class="size-3.5"/><span class="sr-only">Sil</span>
                        </button>
                    </div>
                </div>

                <div class="flex shrink-0 border-b border-ink-200">
                    @foreach ($selectedControls as $tab => $group)
                        <button wire:click="$set('settingsTab', '{{ $tab }}')"
                                class="flex-1 border-b-2 px-2 py-2 text-xs font-medium transition
                                       {{ $settingsTab === $tab ? 'border-brand-600 text-brand-700' : 'border-transparent text-ink-500 hover:text-ink-800' }}">
                            {{ $group['label'] }}
                        </button>
                    @endforeach
                </div>

                <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-3">
                    @php $group = $selectedControls[$settingsTab] ?? reset($selectedControls); @endphp

                    @foreach ($group['controls'] ?? [] as $key => $control)
                        @include('livewire.builder.partials.control', ['key' => $key, 'control' => $control])
                    @endforeach
                </div>
            @endif
        </aside>
    </div>

    {{-- ══ Bölüm düzeni seçici ════════════════════════════════════════════ --}}
    @if ($showLayoutPicker)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/50 p-4"
             wire:click.self="$set('showLayoutPicker', false)" role="dialog" aria-modal="true" aria-label="Bölüm düzeni seçin">
            <div class="w-full max-w-lg rounded-xl bg-white p-5 shadow-xl">
                <h2 class="text-sm font-semibold text-ink-900">Bölüm düzenini seçin</h2>
                <p class="mt-1 text-xs text-ink-500">Sonradan sütun ekleyip çıkarabilir, genişliklerini değiştirebilirsiniz.</p>

                <div class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach ($layouts as $layout)
                        <button wire:click="addSection({{ json_encode($layout['widths']) }}, @js($layoutTargetId))"
                                class="rounded-lg border border-ink-200 p-3 text-left transition hover:border-brand-400 hover:bg-brand-50">
                            <span class="flex h-8 gap-1">
                                @foreach ($layout['widths'] as $width)
                                    <span class="rounded bg-ink-200" style="width: {{ $width }}%"></span>
                                @endforeach
                            </span>
                            <span class="mt-2 block text-[11px] leading-tight text-ink-600">{{ $layout['label'] }}</span>
                        </button>
                    @endforeach
                </div>

                <button wire:click="$set('showLayoutPicker', false)" class="btn-secondary mt-4 w-full">Vazgeç</button>
            </div>
        </div>
    @endif
</div>
