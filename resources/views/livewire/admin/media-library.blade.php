<div class="space-y-4">

    {{-- Yükleme alanı --}}
    <div x-data="{ over: false }"
         @dragover.prevent="over = true"
         @dragleave.prevent="over = false"
         @drop.prevent="over = false; $refs.input.files = $event.dataTransfer.files; $refs.input.dispatchEvent(new Event('change'))"
         :class="over ? 'border-brand-400 bg-brand-50' : 'border-ink-300 bg-white'"
         class="rounded-xl border-2 border-dashed p-6 text-center transition">

        <input type="file" wire:model="uploads" multiple x-ref="input" id="media-upload" class="sr-only">

        <div wire:loading.remove wire:target="uploads">
            <x-icon name="image" class="mx-auto size-8 text-ink-300"/>
            <p class="mt-2 text-sm text-ink-700">
                Dosyaları buraya sürükleyin ya da
                <label for="media-upload" class="cursor-pointer font-medium text-brand-600 hover:underline">bilgisayarınızdan seçin</label>
            </p>
            <p class="mt-1 text-xs text-ink-400">
                Görsel, PDF ve Office dosyaları · en fazla {{ config('cms.media.max_upload_mb') }} MB
                @if ($currentFolder) · <span class="font-medium">{{ $currentFolder->name }}</span> klasörüne @endif
            </p>
        </div>

        <div wire:loading wire:target="uploads" class="py-2">
            <p class="text-sm font-medium text-ink-700">Yükleniyor…</p>
            <p class="mt-1 text-xs text-ink-400">Görseller boyutlandırılıyor, lütfen bekleyin.</p>
        </div>
    </div>

    @if ($uploadErrors)
        <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3">
            <p class="text-sm font-medium text-red-800">Bazı dosyalar yüklenemedi:</p>
            <ul class="mt-1.5 space-y-0.5 text-sm text-red-700">
                @foreach ($uploadErrors as $error)
                    <li>· {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Araç çubuğu --}}
    <div class="flex flex-wrap items-center gap-2">
        <nav aria-label="Klasör yolu" class="flex min-w-0 items-center gap-1 text-sm">
            <button wire:click="openFolder(null)"
                    class="{{ $currentFolder ? 'text-brand-600 hover:underline' : 'font-medium text-ink-900' }}">
                Tüm dosyalar
            </button>
            @if ($currentFolder)
                @foreach ($currentFolder->breadcrumb() as $crumb)
                    <x-icon name="chevron-right" class="size-3.5 shrink-0 text-ink-300"/>
                    <button wire:click="openFolder({{ $crumb->id }})"
                            class="{{ $crumb->id === $currentFolder->id ? 'font-medium text-ink-900' : 'text-brand-600 hover:underline' }}">
                        {{ $crumb->name }}
                    </button>
                @endforeach
            @endif
        </nav>

        <div class="ml-auto flex flex-wrap items-center gap-2">
            <div class="relative">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-ink-400"/>
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Dosya ara"
                       class="field w-44 pl-8" aria-label="Dosya ara">
            </div>

            <select wire:model.live="type" class="field w-auto" aria-label="Dosya türü">
                <option value="">Tüm türler</option>
                <option value="image">Görseller</option>
                <option value="document">Belgeler</option>
                <option value="video">Videolar</option>
            </select>

            <button wire:click="$toggle('creatingFolder')" class="btn-secondary btn-sm">
                <x-icon name="plus" class="size-4"/> Klasör
            </button>
        </div>
    </div>

    @if ($creatingFolder)
        <form wire:submit="createFolder" class="flex items-start gap-2 rounded-lg border border-ink-200 bg-white p-3">
            <div class="flex-1">
                <input type="text" wire:model="newFolderName" placeholder="Klasör adı"
                       class="field @error('newFolderName') field-error @enderror" autofocus>
                @error('newFolderName') <p class="error">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary btn-sm">Oluştur</button>
            <button type="button" wire:click="$set('creatingFolder', false)" class="btn-ghost btn-sm">Vazgeç</button>
        </form>
    @endif

    {{-- Seçim çubuğu --}}
    @if ($selected)
        <div class="flex items-center gap-3 rounded-lg bg-ink-900 px-4 py-2.5 text-sm text-white">
            <span class="font-medium">{{ count($selected) }} dosya seçildi</span>
            <button wire:click="$set('selected', [])" class="text-ink-300 hover:text-white">Seçimi kaldır</button>
            <button wire:click="deleteSelected"
                    wire:confirm="Seçili dosyalar kalıcı olarak silinecek. Sitede kullanılıyorlarsa o alanlar boş kalır. Devam edilsin mi?"
                    class="ml-auto flex items-center gap-1.5 rounded-md bg-red-600 px-2.5 py-1 font-medium hover:bg-red-700">
                <x-icon name="trash" class="size-3.5"/> Sil
            </button>
        </div>
    @endif

    {{-- Klasörler --}}
    @if ($folders->isNotEmpty() && blank($search))
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-6">
            @foreach ($folders as $folder)
                <button wire:click="openFolder({{ $folder->id }})"
                        class="flex items-center gap-2 rounded-lg border border-ink-200 bg-white px-3 py-2.5 text-left transition hover:border-ink-300 hover:bg-ink-50">
                    <svg class="size-5 shrink-0 text-amber-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M2 6a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z"/>
                    </svg>
                    <span class="min-w-0 truncate text-sm text-ink-700">{{ $folder->name }}</span>
                </button>
            @endforeach
        </div>
    @endif

    {{-- Dosyalar --}}
    @if ($items->isEmpty())
        <div class="card flex flex-col items-center justify-center py-16 text-center">
            <x-icon name="image" class="size-10 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">
                {{ filled($search) ? 'Aramanızla eşleşen dosya yok.' : 'Bu klasör boş.' }}
            </p>
            <p class="mt-1 text-sm text-ink-400">
                {{ filled($search) ? 'Farklı bir kelime deneyin.' : 'Yukarıdaki alandan dosya yükleyebilirsiniz.' }}
            </p>
        </div>
    @else
        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">
            @foreach ($items as $item)
                <li class="group relative">
                    <label class="absolute top-2 left-2 z-10 cursor-pointer opacity-0 transition group-hover:opacity-100 has-checked:opacity-100">
                        <input type="checkbox" wire:model.live="selected" value="{{ $item->id }}"
                               class="size-4 rounded border-white/60 bg-white/90 text-brand-600 shadow-sm focus:ring-brand-500"
                               aria-label="{{ $item->original_name }} seç">
                    </label>

                    <button wire:click="edit({{ $item->id }})"
                            class="block w-full overflow-hidden rounded-lg border border-ink-200 bg-white text-left transition hover:border-brand-400 hover:shadow-sm">
                        <span class="flex aspect-square items-center justify-center bg-ink-50">
                            @if ($item->isImage())
                                <img src="{{ $item->url('thumb') }}" alt="{{ $item->altText() }}"
                                     class="size-full object-cover" loading="lazy">
                            @else
                                <span class="flex flex-col items-center gap-1 text-ink-400">
                                    <x-icon name="file-text" class="size-7"/>
                                    <span class="text-[10px] font-semibold uppercase">{{ $item->extension }}</span>
                                </span>
                            @endif
                        </span>
                        <span class="block px-2 py-1.5">
                            <span class="block truncate text-xs text-ink-700">{{ $item->original_name }}</span>
                            <span class="block text-[11px] text-ink-400">{{ $item->humanSize() }}</span>
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center justify-between text-xs text-ink-500">
            <span>{{ number_format($usage['count']) }} dosya · {{ round($usage['size'] / 1048576, 1) }} MB</span>
        </div>

        {{ $items->links() }}
    @endif

    {{-- Dosya düzenleme --}}
    @if ($editingId)
        @php $media = \App\Models\Media::find($editingId); @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-ink-950/50 p-4"
             wire:click.self="$set('editingId', null)" role="dialog" aria-modal="true" aria-label="Dosya bilgileri">
            <div class="max-h-full w-full max-w-lg overflow-y-auto rounded-xl bg-white shadow-xl">
                <div class="flex items-center justify-between border-b border-ink-100 px-5 py-3.5">
                    <h2 class="text-sm font-semibold text-ink-900">Dosya bilgileri</h2>
                    <button wire:click="$set('editingId', null)" class="btn-ghost p-1.5" aria-label="Kapat">
                        <x-icon name="chevron-right" class="size-4 rotate-45"/>
                    </button>
                </div>

                <div class="space-y-4 p-5">
                    @if ($media?->isImage())
                        <img src="{{ $media->url('medium') }}" alt=""
                             class="mx-auto max-h-56 rounded-lg border border-ink-200 object-contain">
                    @endif

                    <dl class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs">
                        <dt class="text-ink-500">Dosya adı</dt>
                        <dd class="truncate text-ink-800">{{ $media?->original_name }}</dd>
                        <dt class="text-ink-500">Boyut</dt>
                        <dd class="text-ink-800">
                            {{ $media?->humanSize() }}
                            @if ($media?->width) · {{ $media->width }}×{{ $media->height }} @endif
                        </dd>
                    </dl>

                    <div class="space-y-3">
                        @foreach ($locales as $locale)
                            <div>
                                <label class="label" for="alt-{{ $locale->code }}">
                                    Alternatif metin
                                    @if ($locales->count() > 1)
                                        <span class="font-normal text-ink-400">({{ $locale->native_name }})</span>
                                    @endif
                                </label>
                                <input id="alt-{{ $locale->code }}" type="text"
                                       wire:model="editing.alt.{{ $locale->code }}" class="field">
                            </div>
                        @endforeach
                        <p class="hint">
                            Görselin ne anlattığını kısaca yazın. Görme engelli ziyaretçiler için okunur,
                            görsel yüklenemediğinde yerine görünür ve arama motorlarının görseli anlamasını sağlar.
                        </p>
                    </div>

                    <div>
                        <label class="label" for="media-url">Dosya adresi</label>
                        <input id="media-url" type="text" readonly value="{{ $media?->url() }}"
                               class="field bg-ink-50 text-xs" onfocus="this.select()">
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-ink-100 px-5 py-3">
                    <button wire:click="$set('editingId', null)" class="btn-secondary">Vazgeç</button>
                    <button wire:click="saveEdit" class="btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    @endif
</div>
