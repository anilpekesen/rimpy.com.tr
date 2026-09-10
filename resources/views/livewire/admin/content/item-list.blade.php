<div class="space-y-4">
    <div class="flex flex-wrap items-center gap-2">
        <div class="flex rounded-lg border border-ink-200 bg-white p-0.5 text-sm">
            @foreach (['' => 'Tümü', 'published' => 'Yayında', 'draft' => 'Taslak'] as $value => $label)
                <button wire:click="$set('status', '{{ $value }}')"
                        class="rounded-md px-3 py-1.5 font-medium transition
                               {{ $status === $value ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-ink-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-ink-400"/>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Ara" class="field w-44 pl-8" aria-label="Ara">
        </div>

        @if ($terms->isNotEmpty())
            <select wire:model.live="termId" class="field w-auto" aria-label="Kategoriye göre süz">
                <option value="">Tüm kategoriler</option>
                @foreach ($terms as $term)
                    <option value="{{ $term->id }}">{{ $term->t('name') }}</option>
                @endforeach
            </select>
        @endif

        <div class="ml-auto flex gap-2">
            @foreach ($type->taxonomies as $taxonomy)
                <a href="{{ route('admin.terms.index', [$type->key, $taxonomy->key]) }}" wire:navigate class="btn-secondary">
                    {{ $taxonomy->pluralLabel('tr') }}
                </a>
            @endforeach
            <button wire:click="$set('creating', true)" class="btn-primary">
                <x-icon name="plus" class="size-4"/> Yeni {{ $type->label('tr') }}
            </button>
        </div>
    </div>

    @if ($creating)
        <form wire:submit="create" class="card flex flex-wrap items-start gap-3 p-4">
            <div class="min-w-56 flex-1">
                <label for="new-item" class="label">{{ $type->label('tr') }} başlığı</label>
                <input id="new-item" type="text" wire:model="newTitle"
                       class="field @error('newTitle') field-error @enderror" autofocus>
                @error('newTitle') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2 pt-6.5">
                <button type="submit" class="btn-primary">Oluştur</button>
                <button type="button" wire:click="$set('creating', false)" class="btn-ghost">Vazgeç</button>
            </div>
        </form>
    @endif

    @if ($items->isEmpty())
        <div class="card flex flex-col items-center justify-center py-16 text-center">
            <x-icon name="{{ $type->icon }}" class="size-10 text-ink-200"/>
            <p class="mt-3 text-sm font-medium text-ink-700">
                {{ filled($search) || $status || $termId ? 'Eşleşen kayıt yok.' : 'Henüz '.mb_strtolower($type->label('tr')).' eklenmedi.' }}
            </p>
            <button wire:click="$set('creating', true)" class="btn-primary mt-4">
                <x-icon name="plus" class="size-4"/> İlkini ekle
            </button>
        </div>
    @else
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <caption class="sr-only">{{ $type->pluralLabel('tr') }}</caption>
                <thead>
                    <tr class="border-b border-ink-100 bg-ink-50/60 text-left text-xs font-medium text-ink-500">
                        <th scope="col" class="px-4 py-2.5">Başlık</th>
                        <th scope="col" class="hidden px-4 py-2.5 md:table-cell">Kategori</th>
                        <th scope="col" class="px-4 py-2.5">Durum</th>
                        <th scope="col" class="hidden px-4 py-2.5 sm:table-cell">Tarih</th>
                        <th scope="col" class="px-4 py-2.5"><span class="sr-only">İşlemler</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    @foreach ($items as $item)
                        <tr class="group hover:bg-ink-50/60">
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    @if ($item->featuredImage)
                                        <img src="{{ $item->featuredImage->url('thumb') }}" alt=""
                                             class="size-9 shrink-0 rounded object-cover">
                                    @else
                                        <span class="flex size-9 shrink-0 items-center justify-center rounded bg-ink-100">
                                            <x-icon name="image" class="size-4 text-ink-300"/>
                                        </span>
                                    @endif
                                    <a href="{{ route('admin.content.edit', [$type->key, $item->id]) }}" wire:navigate
                                       class="min-w-0 truncate font-medium text-ink-900 hover:text-brand-600 hover:underline">
                                        {{ $item->t('title') ?: 'Adsız' }}
                                    </a>
                                    @if ($item->is_featured)
                                        <span class="badge-amber shrink-0">Öne çıkan</span>
                                    @endif
                                </div>
                            </td>
                            <td class="hidden px-4 py-2.5 text-xs text-ink-500 md:table-cell">
                                {{ $item->terms->map(fn ($t) => $t->t('name'))->implode(', ') ?: '—' }}
                            </td>
                            <td class="px-4 py-2.5">
                                <button wire:click="togglePublish({{ $item->id }})"
                                        class="{{ $item->status === 'published' ? 'badge-green' : 'badge-amber' }} transition hover:opacity-80">
                                    {{ $item->status === 'published' ? 'Yayında' : 'Taslak' }}
                                </button>
                            </td>
                            <td class="hidden px-4 py-2.5 text-xs text-ink-500 sm:table-cell">
                                {{ $item->published_at?->format('d.m.Y') ?? '—' }}
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex justify-end gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                                    <a href="{{ route('admin.content.edit', [$type->key, $item->id]) }}" wire:navigate class="btn-ghost btn-sm">
                                        <x-icon name="edit" class="size-4"/><span class="sr-only">Düzenle</span>
                                    </a>
                                    @if ($type->supports('builder'))
                                        <a href="{{ route('admin.builder.content', $item->id) }}" class="btn-ghost btn-sm" title="Tasarla">
                                            <x-icon name="blocks" class="size-4"/><span class="sr-only">Tasarla</span>
                                        </a>
                                    @endif
                                    <button wire:click="toggleFeatured({{ $item->id }})" class="btn-ghost btn-sm" title="Öne çıkar">
                                        <x-icon name="gauge" class="size-4"/><span class="sr-only">Öne çıkar</span>
                                    </button>
                                    <button wire:click="delete({{ $item->id }})"
                                            wire:confirm="“{{ $item->t('title') }}” silinecek. Devam edilsin mi?"
                                            class="btn-ghost btn-sm text-red-600 hover:bg-red-50">
                                        <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $items->links() }}
    @endif
</div>
