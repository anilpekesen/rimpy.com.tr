<div class="max-w-3xl space-y-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.content.index', $type->key) }}" wire:navigate class="btn-ghost btn-sm">
            <x-icon name="chevron-right" class="size-4 rotate-180"/> {{ $type->pluralLabel('tr') }}
        </a>
    </div>

    <div class="card overflow-hidden">
        @if ($terms->isEmpty())
            <p class="px-4 py-12 text-center text-sm text-ink-400">
                Henüz {{ mb_strtolower($taxonomy->pluralLabel('tr')) }} eklenmedi.
            </p>
        @else
            <ul class="divide-y divide-ink-100">
                @foreach ($terms as $term)
                    <li>
                        <div class="group flex items-center gap-2 px-4 py-2.5">
                            <span class="min-w-0 flex-1 truncate text-sm font-medium text-ink-900">{{ $term->t('name') }}</span>
                            <span class="shrink-0 text-xs text-ink-400">{{ $term->items_count }} kayıt</span>
                            <div class="flex shrink-0 gap-1 opacity-0 transition group-hover:opacity-100 focus-within:opacity-100">
                                <button wire:click="edit({{ $term->id }})" class="btn-ghost btn-sm">
                                    <x-icon name="edit" class="size-4"/><span class="sr-only">Düzenle</span>
                                </button>
                                <button wire:click="delete({{ $term->id }})"
                                        wire:confirm="“{{ $term->t('name') }}” silinecek. Bu kategorideki içerikler silinmez. Devam edilsin mi?"
                                        class="btn-ghost btn-sm text-red-600 hover:bg-red-50">
                                    <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                                </button>
                            </div>
                        </div>

                        @foreach ($term->children as $child)
                            <div class="group flex items-center gap-2 border-t border-ink-50 py-2 pr-4 pl-10">
                                <span class="min-w-0 flex-1 truncate text-sm text-ink-700">{{ $child->t('name') }}</span>
                                <div class="flex shrink-0 gap-1 opacity-0 transition group-hover:opacity-100">
                                    <button wire:click="edit({{ $child->id }})" class="btn-ghost btn-sm">
                                        <x-icon name="edit" class="size-4"/><span class="sr-only">Düzenle</span>
                                    </button>
                                    <button wire:click="delete({{ $child->id }})" class="btn-ghost btn-sm text-red-600">
                                        <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <form wire:submit="save" class="card-pad space-y-4">
        <h2 class="text-sm font-semibold text-ink-900">
            {{ $editingId ? 'Düzenle' : 'Yeni '.$taxonomy->label('tr') }}
        </h2>

        @foreach ($locales as $locale)
            <div>
                <label for="tn-{{ $locale->code }}" class="label">
                    Ad @if ($locales->count() > 1) <span class="font-normal text-ink-400">({{ $locale->native_name }})</span> @endif
                </label>
                <input id="tn-{{ $locale->code }}" type="text" wire:model="names.{{ $locale->code }}"
                       class="field @error('names.'.$locale->code) field-error @enderror">
                @error('names.'.$locale->code) <p class="error">{{ $message }}</p> @enderror
            </div>
        @endforeach

        @if ($taxonomy->is_hierarchical)
            <div>
                <label for="t-parent" class="label">Üst kategori</label>
                <select id="t-parent" wire:model="parentId" class="field">
                    <option value="">— Yok —</option>
                    @foreach ($allTerms as $option)
                        @continue ($option->id === $editingId)
                        <option value="{{ $option->id }}">{{ $option->t('name') }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="flex gap-2">
            <button type="submit" class="btn-primary">{{ $editingId ? 'Güncelle' : 'Ekle' }}</button>
            @if ($editingId)
                <button type="button" wire:click="$set('editingId', null)" class="btn-ghost">Vazgeç</button>
            @endif
        </div>
    </form>
</div>
