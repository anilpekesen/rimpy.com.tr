<div>
    @if ($isOpen)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-ink-950/55 p-3 sm:p-6"
             role="dialog" aria-modal="true" aria-labelledby="media-picker-title"
             wire:key="global-media-picker" @keydown.escape.window="$wire.closePicker()">
            <button type="button" wire:click="closePicker" class="absolute inset-0 cursor-default" tabindex="-1" aria-label="Medya seçiciyi kapat"></button>

            <section class="relative flex max-h-[min(46rem,calc(100vh-2rem))] w-full max-w-5xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">
                <header class="flex shrink-0 items-center gap-3 border-b border-ink-200 px-4 py-3 sm:px-5">
                    <div class="min-w-0 flex-1">
                        <h2 id="media-picker-title" class="text-sm font-semibold text-ink-900">
                            {{ $multiple ? 'Görselleri seçin' : ($accept === 'image' ? 'Görsel seçin' : 'Dosya seçin') }}
                        </h2>
                        <p class="mt-0.5 text-xs text-ink-500">Kütüphaneden seçin veya yeni dosya yükleyin.</p>
                    </div>
                    <button type="button" wire:click="closePicker" class="btn-ghost p-2" aria-label="Kapat">×</button>
                </header>

                <div class="flex shrink-0 flex-col gap-2 border-b border-ink-100 bg-ink-50/70 p-3 sm:flex-row sm:items-center sm:px-5">
                    <input type="search" wire:model.live.debounce.250ms="search" class="field sm:max-w-xs"
                           placeholder="Dosya adıyla ara" aria-label="Medyada ara" autofocus>

                    <label class="btn-secondary cursor-pointer sm:ml-auto">
                        <x-icon name="plus" class="size-4"/>
                        <span wire:loading.remove wire:target="uploads">{{ $multiple ? 'Görsel yükle' : 'Yeni dosya yükle' }}</span>
                        <span wire:loading wire:target="uploads">Yükleniyor…</span>
                        <input type="file" wire:model="uploads" class="sr-only" @if($multiple) multiple @endif
                               accept="{{ $accept === 'image' ? 'image/*' : ($accept === 'video' ? 'video/*' : '.pdf,.doc,.docx,.xls,.xlsx') }}">
                    </label>
                </div>

                @if ($uploadErrors)
                    <div class="mx-4 mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" role="alert">
                        @foreach ($uploadErrors as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif

                <div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-5">
                    @if ($items->isEmpty())
                        <div class="flex min-h-56 flex-col items-center justify-center text-center">
                            <x-icon name="image" class="size-9 text-ink-200"/>
                            <p class="mt-3 text-sm font-medium text-ink-700">{{ filled($search) ? 'Eşleşen dosya bulunamadı' : 'Medya kütüphanesi boş' }}</p>
                            <p class="mt-1 text-xs text-ink-500">{{ filled($search) ? 'Başka bir arama deneyin.' : 'Yukarıdaki düğmeyle ilk dosyanızı yükleyin.' }}</p>
                        </div>
                    @else
                        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                            @foreach ($items as $item)
                                @php $checked = in_array($item->id, $selected, true); @endphp
                                <li>
                                    <button type="button" wire:click="{{ $multiple ? 'toggle' : 'choose' }}({{ $item->id }})"
                                            class="group block w-full overflow-hidden rounded-lg text-left ring-offset-2 transition {{ $checked ? 'ring-2 ring-brand-600' : 'ring-1 ring-ink-200 hover:ring-brand-400' }}"
                                            aria-pressed="{{ $checked ? 'true' : 'false' }}">
                                        <span class="relative flex aspect-square items-center justify-center bg-ink-50">
                                            @if ($item->isImage())
                                                <img src="{{ $item->url('thumb') }}" alt="{{ $item->altText() }}" class="size-full object-cover" loading="lazy">
                                            @else
                                                <x-icon name="file-text" class="size-8 text-ink-300"/>
                                            @endif
                                            @if ($multiple)
                                                <span class="absolute top-2 right-2 flex size-5 items-center justify-center rounded-full border-2 border-white text-[11px] font-bold shadow-sm {{ $checked ? 'bg-brand-600 text-white' : 'bg-white text-transparent' }}">✓</span>
                                            @endif
                                        </span>
                                        <span class="block truncate px-2 py-1.5 text-xs text-ink-700">{{ $item->original_name }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                @if ($multiple)
                    <footer class="flex shrink-0 items-center justify-between gap-3 border-t border-ink-200 px-4 py-3 sm:px-5">
                        <span class="text-xs text-ink-500">{{ count($selected) }} görsel seçili</span>
                        <div class="flex gap-2">
                            <button type="button" wire:click="closePicker" class="btn-secondary">Vazgeç</button>
                            <button type="button" wire:click="applySelection" class="btn-primary" @disabled(! count($selected))>Seçilenleri ekle</button>
                        </div>
                    </footer>
                @endif
            </section>
        </div>
    @endif
</div>
