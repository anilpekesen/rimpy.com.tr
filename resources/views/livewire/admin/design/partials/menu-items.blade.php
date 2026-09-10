@foreach ($items->sortBy('sort') as $item)
    <li data-menu-item="{{ $item->id }}" class="rounded-lg border border-ink-200 bg-white">
        <div class="group flex items-center gap-2 px-3 py-2.5">
            <button type="button" data-menu-handle
                    class="cursor-grab touch-none rounded-md p-1.5 text-ink-400 hover:bg-ink-100 hover:text-ink-700 active:cursor-grabbing"
                    title="Sürükleyerek sırala" aria-label="{{ $item->title() }} öğesini sürükle">
                <svg class="size-4" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                    <circle cx="5" cy="3" r="1"/><circle cx="11" cy="3" r="1"/>
                    <circle cx="5" cy="8" r="1"/><circle cx="11" cy="8" r="1"/>
                    <circle cx="5" cy="13" r="1"/><circle cx="11" cy="13" r="1"/>
                </svg>
            </button>

            <span class="min-w-0 flex-1">
                <span class="block truncate text-sm font-medium text-ink-900 {{ $item->is_active ? '' : 'line-through opacity-50' }}">
                    {{ $item->title() }}
                </span>
                <span class="block truncate font-mono text-[11px] text-ink-500">
                    {{ $item->href() ?? 'Hedef bulunamadı, menüde gösterilmiyor' }}
                </span>
            </span>

            <div class="flex shrink-0 gap-0.5 opacity-70 transition group-hover:opacity-100 group-focus-within:opacity-100">
                <button wire:click="editItem({{ $item->id }})" class="btn-ghost btn-sm" title="Düzenle">
                    <x-icon name="edit" class="size-4"/><span class="sr-only">Düzenle</span>
                </button>
                <button wire:click="deleteItem({{ $item->id }})"
                        wire:confirm="“{{ $item->title() }}” ve varsa alt öğeleri menüden kaldırılacak. Devam edilsin mi?"
                        class="btn-ghost btn-sm text-red-600 hover:bg-red-50" title="Sil">
                    <x-icon name="trash" class="size-4"/><span class="sr-only">Sil</span>
                </button>
            </div>
        </div>

        @if ($depth < 3)
            <ol data-menu-sortable data-parent-id="{{ $item->id }}"
                class="menu-dropzone ml-7 space-y-2 border-t border-ink-100 px-2 py-2">
                @include('livewire.admin.design.partials.menu-items', [
                    'items' => $allItems->where('parent_id', $item->id),
                    'allItems' => $allItems,
                    'depth' => $depth + 1,
                ])
            </ol>
        @endif
    </li>
@endforeach
