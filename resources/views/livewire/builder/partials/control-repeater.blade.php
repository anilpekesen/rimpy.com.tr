@php
    $rows = is_array($value) ? array_values($value) : [];
    $titleField = $control['title_field'] ?? array_key_first($control['fields']);
@endphp

<div class="space-y-2" wire:key="{{ $selected['id'] }}-{{ $key }}-rep"
     x-data="{ open: null }">

    @foreach ($rows as $index => $row)
        <div class="overflow-hidden rounded-lg border border-ink-200">
            <div class="flex items-center gap-1 bg-ink-50 px-2 py-1.5">
                <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}"
                        class="flex min-w-0 flex-1 items-center gap-1.5 text-left">
                    <x-icon name="chevron-down" class="size-3.5 shrink-0 text-ink-400 transition"
                            ::class="open === {{ $index }} && 'rotate-180'"/>
                    <span class="truncate text-xs font-medium text-ink-700">
                        {{ \Illuminate\Support\Str::limit(strip_tags((string) ($row[$titleField] ?? '')), 28) ?: 'Satır '.($index + 1) }}
                    </span>
                </button>

                <button type="button" class="btn-ghost btn-sm shrink-0" title="Yukarı taşı" @disabled($index === 0)
                        wire:click="setSetting('{{ $key }}', @js($index > 0 ? tap($rows, function (&$r) use ($index) { [$r[$index - 1], $r[$index]] = [$r[$index], $r[$index - 1]]; }) : $rows), false)">↑</button>
                <button type="button" class="btn-ghost btn-sm shrink-0" title="Aşağı taşı" @disabled($index === count($rows) - 1)
                        wire:click="setSetting('{{ $key }}', @js($index < count($rows) - 1 ? tap($rows, function (&$r) use ($index) { [$r[$index + 1], $r[$index]] = [$r[$index], $r[$index + 1]]; }) : $rows), false)">↓</button>
                <button type="button" class="btn-ghost btn-sm shrink-0 text-red-600 hover:bg-red-50" title="Satırı sil"
                        wire:click="setSetting('{{ $key }}', @js(array_values(array_filter($rows, fn ($r, $i) => $i !== $index, ARRAY_FILTER_USE_BOTH))), false)">
                    <x-icon name="trash" class="size-3.5"/>
                </button>
            </div>

            <div x-show="open === {{ $index }}" x-collapse class="space-y-2.5 border-t border-ink-100 p-2.5">
                @foreach ($control['fields'] as $fieldKey => $field)
                    @include('livewire.builder.partials.repeater-field', [
                        'rowIndex' => $index,
                        'rows' => $rows,
                        'repeaterKey' => $key,
                        'fieldKey' => $fieldKey,
                        'field' => $field,
                        'fieldValue' => $row[$fieldKey] ?? ($field['default'] ?? null),
                    ])
                @endforeach
            </div>
        </div>
    @endforeach

    @php
        $blank = [];
        foreach ($control['fields'] as $fk => $f) { $blank[$fk] = $f['default'] ?? ''; }
    @endphp
    <button type="button" class="btn-secondary btn-sm w-full"
            wire:click="setSetting('{{ $key }}', @js([...$rows, $blank]), false)">
        <x-icon name="plus" class="size-3.5"/> {{ $control['add_label'] ?? 'Satır ekle' }}
    </button>
</div>
