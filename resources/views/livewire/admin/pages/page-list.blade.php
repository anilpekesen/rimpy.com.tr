<div class="space-y-4">
    <div class="flex flex-wrap items-center gap-2">
        <div class="flex rounded-lg border border-ink-200 bg-white p-0.5 text-sm">
            @foreach ([
                '' => ['Tümü', $counts['all']],
                'published' => ['Yayında', $counts['published']],
                'draft' => ['Taslak', $counts['draft']],
            ] as $value => [$label, $count])
                <button wire:click="$set('status', '{{ $value }}')"
                        class="rounded-md px-3 py-1.5 font-medium transition
                               {{ $status === $value ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-ink-100' }}">
                    {{ $label }}
                    <span class="{{ $status === $value ? 'text-ink-300' : 'text-ink-400' }}">{{ $count }}</span>
                </button>
            @endforeach
        </div>

        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-ink-400"/>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Sayfa ara"
                   class="field w-48 pl-8" aria-label="Sayfa ara">
        </div>

        <button wire:click="$set('creating', true)" class="btn-primary ml-auto">
            <x-icon name="plus" class="size-4"/> Yeni sayfa
        </button>
    </div>

    @if ($creating)
        <form wire:submit="createPage" class="card flex flex-wrap items-start gap-3 p-4">
            <div class="min-w-56 flex-1">
                <label for="new-title" class="label">Sayfa başlığı</label>
                <input id="new-title" type="text" wire:model="newTitle"
                       class="field @error('newTitle') field-error @enderror"
                       placeholder="Örnek: Hakkımızda" autofocus>
                @error('newTitle')
                    <p class="error">{{ $message }}</p>
                @else
                    <p class="hint">Sayfayı oluşturunca doğrudan tasarım ekranı açılır.</p>
                @enderror
            </div>
            <div class="flex gap-2 pt-6.5">
                <button type="submit" class="btn-primary">Oluştur ve tasarla</button>
                <button type="button" wire:click="$set('creating', false)" class="btn-ghost">Vazgeç</button>
            </div>
        </form>
    @endif

    @if ($pages->isEmpty())
        <div class="card flex flex-col items-center justify-center py-16 text-center">
            <x-icon name="file-text" class="size-10 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">
                {{ filled($search) || $status ? 'Eşleşen sayfa yok.' : 'Henüz sayfa oluşturulmadı.' }}
            </p>
            <p class="mt-1 max-w-sm text-sm text-ink-400">
                Sayfalar sitenizin ana bölümleridir: Hakkımızda, Hizmetler, İletişim gibi.
            </p>
            <button wire:click="$set('creating', true)" class="btn-primary mt-4">
                <x-icon name="plus" class="size-4"/> İlk sayfayı oluştur
            </button>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <caption class="sr-only">Site sayfaları</caption>
                <thead>
                    <tr class="border-b border-ink-100 bg-ink-50/60 text-left text-xs font-medium text-ink-500">
                        <th scope="col" class="px-4 py-2.5">Başlık</th>
                        <th scope="col" class="hidden px-4 py-2.5 lg:table-cell">Adres</th>
                        <th scope="col" class="px-4 py-2.5">Durum</th>
                        <th scope="col" class="hidden px-4 py-2.5 sm:table-cell">Güncellendi</th>
                        <th scope="col" class="px-4 py-2.5"><span class="sr-only">İşlemler</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($pages as $page)
                        <tr class="group hover:bg-ink-50/60">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if ($page->is_home)
                                        <span class="badge-gray shrink-0" title="Ana sayfa">Ana sayfa</span>
                                    @endif
                                    <a href="{{ route('admin.builder.page', $page) }}"
                                       class="truncate font-medium text-ink-900 hover:text-brand-600 hover:underline">
                                        {{ $page->t('title') ?: 'Adsız sayfa' }}
                                    </a>
                                </div>
                            </td>

                            <td class="hidden px-4 py-3 lg:table-cell">
                                <span class="font-mono text-xs text-ink-400">{{ $page->path() }}</span>
                            </td>

                            <td class="px-4 py-3">
                                <button wire:click="togglePublish({{ $page->id }})"
                                        class="{{ $page->status === 'published' ? 'badge-green' : 'badge-amber' }} transition hover:opacity-80"
                                        title="{{ $page->status === 'published' ? 'Yayından kaldır' : 'Yayına al' }}">
                                    {{ $page->statusLabel() }}
                                </button>
                            </td>

                            <td class="hidden px-4 py-3 text-xs text-ink-500 sm:table-cell">
                                {{ $page->updated_at?->diffForHumans() }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                                    <a href="{{ route('admin.builder.page', $page) }}" class="btn-ghost btn-sm" title="Tasarla">
                                        <x-icon name="edit" class="size-4"/><span class="sr-only">Tasarla</span>
                                    </a>
                                    <a href="{{ route('admin.pages.settings', $page) }}" wire:navigate class="btn-ghost btn-sm" title="Ayarlar">
                                        <x-icon name="settings" class="size-4"/><span class="sr-only">Ayarlar</span>
                                    </a>
                                    @if ($page->status === 'published')
                                        <a href="{{ $page->path() }}" target="_blank" rel="noopener" class="btn-ghost btn-sm" title="Sitede gör">
                                            <x-icon name="eye" class="size-4"/><span class="sr-only">Sitede gör</span>
                                        </a>
                                    @endif
                                    <button wire:click="duplicate({{ $page->id }})" class="btn-ghost btn-sm" title="Kopyala">
                                        <x-icon name="blocks" class="size-4"/><span class="sr-only">Kopyala</span>
                                    </button>
                                    @unless ($page->is_home)
                                        <button wire:click="setHome({{ $page->id }})" class="btn-ghost btn-sm" title="Ana sayfa yap">
                                            <x-icon name="gauge" class="size-4"/><span class="sr-only">Ana sayfa yap</span>
                                        </button>
                                        <button wire:click="delete({{ $page->id }})"
                                                wire:confirm="“{{ $page->t('title') }}” sayfası silinecek. Devam edilsin mi?"
                                                class="btn-ghost btn-sm text-red-600 hover:bg-red-50" title="Sil">
                                            <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                                        </button>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $pages->links() }}
    @endif
</div>
