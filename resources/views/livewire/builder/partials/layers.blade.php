@php
/**
 * Katman ağacı: sayfanın yapısını iç içe bir liste olarak gösterir.
 * Tuvalde üst üste binen ya da görünmez hâle gelmiş öğelere buradan
 * ulaşılabilir.
 */
$registry = app(\App\Builder\WidgetRegistry::class);
@endphp

<ul class="space-y-0.5">
    @foreach ($nodes as $node)
        @php
            $label = match ($node['type']) {
                'section' => 'Bölüm',
                'column' => 'Sütun · %'.round($node['settings']['width']['d'] ?? 100),
                default => $registry->find($node['widget'] ?? null)?->label() ?? 'Öğe',
            };
            $isSelected = $selectedId === $node['id'];
        @endphp

        <li>
            <div class="group flex items-center gap-1 rounded-md pr-0.5 transition
                        {{ $isSelected ? 'bg-brand-50' : 'hover:bg-ink-100' }}"
                 style="padding-left: {{ $depth * 12 }}px">

                <button wire:click="selectElement('{{ $node['id'] }}')"
                        class="min-w-0 flex-1 truncate px-1.5 py-1 text-left text-xs
                               {{ $isSelected ? 'font-medium text-brand-700' : 'text-ink-600' }}">
                    {{ $label }}
                </button>

                <button wire:click="duplicateElement('{{ $node['id'] }}')"
                        class="btn-ghost shrink-0 p-1 opacity-0 group-hover:opacity-100" title="Kopyala">
                    <x-icon name="blocks" class="size-3"/><span class="sr-only">Kopyala</span>
                </button>
                <button wire:click="deleteElement('{{ $node['id'] }}')"
                        wire:confirm="Bu öğe ve içindekiler silinecek. Devam edilsin mi?"
                        class="btn-ghost shrink-0 p-1 text-red-600 opacity-0 group-hover:opacity-100" title="Sil">
                    <x-icon name="trash" class="size-3"/><span class="sr-only">Sil</span>
                </button>
            </div>

            @if (! empty($node['elements']))
                @include('livewire.builder.partials.layers', ['nodes' => $node['elements'], 'depth' => $depth + 1])
            @endif
        </li>
    @endforeach
</ul>
