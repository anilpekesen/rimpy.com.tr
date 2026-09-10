@php
/**
 * Tekrarlayan listenin bir satırındaki tek alan.
 *
 * Değer değişince yalnızca o alanın koordinatı (liste anahtarı, satır sırası,
 * alan adı) sunucuya gider; listenin tamamı istemciye gömülmez.
 */
$fieldType = $field['type'] ?? 'text';
$fieldId = "rep-{$repeaterKey}-{$rowIndex}-{$fieldKey}";

// Alanın değerini sunucuya yazan ortak çağrı.
$write = "\$wire.setRepeaterField('".e($repeaterKey)."', {$rowIndex}, '".e($fieldKey)."', ";
@endphp

<div class="space-y-1" wire:key="{{ $repeaterKey }}-{{ $rowIndex }}-{{ $fieldKey }}">
    @if ($fieldType !== 'toggle')
        <label for="{{ $fieldId }}" class="block text-[11px] font-medium text-ink-600">{{ $field['label'] }}</label>
    @endif

    @switch ($fieldType)
        @case('textarea')
            <textarea id="{{ $fieldId }}" rows="{{ $field['rows'] ?? 2 }}" class="field px-2 py-1.5 text-xs"
                      x-on:input.debounce.500ms="{!! $write !!}$event.target.value)">{{ is_scalar($fieldValue) ? $fieldValue : '' }}</textarea>
            @break

        @case('rich')
            <textarea id="{{ $fieldId }}" rows="4" class="field px-2 py-1.5 font-mono text-[11px]"
                      x-on:input.debounce.500ms="{!! $write !!}$event.target.value)">{{ is_scalar($fieldValue) ? $fieldValue : '' }}</textarea>
            <p class="text-[10px] text-ink-400">Basit HTML kullanabilirsiniz: &lt;p&gt;, &lt;strong&gt;, &lt;a&gt;</p>
            @break

        @case('select')
            <select id="{{ $fieldId }}" class="field px-2 py-1.5 text-xs"
                    x-on:change="{!! $write !!}$event.target.value)">
                @foreach ($field['options'] as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected($fieldValue == $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            @break

        @case('toggle')
            <label class="flex items-center gap-2 text-xs text-ink-700">
                <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600" @checked($fieldValue)
                       x-on:change="{!! $write !!}$event.target.checked)">
                {{ $field['label'] }}
            </label>
            @break

        @case('media')
            @php $rowMedia = is_numeric($fieldValue) ? \App\Models\Media::find($fieldValue) : null; @endphp
            <div class="flex items-center gap-2">
                @if ($rowMedia)
                    <img src="{{ $rowMedia->url('thumb') }}" alt="" class="size-10 shrink-0 rounded border border-ink-200 object-cover">
                @endif
                <button type="button" class="btn-secondary btn-sm flex-1"
                        @click="$dispatch('open-media-picker', {
                            repeater: @js($repeaterKey), row: {{ $rowIndex }},
                            field: @js($fieldKey), accept: 'image'
                        })">
                    {{ $rowMedia ? 'Değiştir' : 'Görsel seç' }}
                </button>
                @if ($rowMedia)
                    <button type="button" class="btn-ghost btn-sm text-red-600"
                            x-on:click="{!! $write !!}null)" title="Kaldır">×</button>
                @endif
            </div>
            @break

        @case('link')
            <input id="{{ $fieldId }}" type="text" class="field px-2 py-1.5 text-xs" placeholder="https://…"
                   value="{{ is_array($fieldValue) ? ($fieldValue['url'] ?? '') : $fieldValue }}"
                   x-on:input.debounce.500ms="{!! $write !!}{ type: 'url', url: $event.target.value, target: '_self' })">
            @break

        @case('icon')
            <select id="{{ $fieldId }}" class="field px-2 py-1.5 text-xs"
                    x-on:change="{!! $write !!}$event.target.value)">
                @foreach ([
                    '' => 'Yok', 'check' => 'Onay', 'check-circle' => 'Onay (daire)', 'star' => 'Yıldız',
                    'arrow-right' => 'Sağ ok', 'phone' => 'Telefon', 'mail' => 'Zarf',
                    'map-pin' => 'Konum', 'shield' => 'Kalkan', 'award' => 'Ödül',
                ] as $iconValue => $iconLabel)
                    <option value="{{ $iconValue }}" @selected($fieldValue === $iconValue)>{{ $iconLabel }}</option>
                @endforeach
            </select>
            @break

        @default
            <input id="{{ $fieldId }}" type="text" class="field px-2 py-1.5 text-xs"
                   value="{{ is_scalar($fieldValue) ? $fieldValue : '' }}"
                   x-on:input.debounce.500ms="{!! $write !!}$event.target.value)">
    @endswitch
</div>
