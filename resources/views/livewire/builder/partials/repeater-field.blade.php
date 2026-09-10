@php
/**
 * Tekrarlayan listenin bir satırındaki tek alan.
 *
 * Değer değiştiğinde satırların tamamı yeniden yazılır; kısmi güncelleme
 * yapmak yerine tüm listeyi göndermek, sıralama ve silme işlemleriyle
 * çakışmayı tamamen ortadan kaldırır.
 */
$fieldType = $field['type'] ?? 'text';
$fieldId = "rep-{$repeaterKey}-{$rowIndex}-{$fieldKey}";

// Alanın yeni değeriyle güncellenmiş satır dizisini üreten JS ifadesi.
$update = "(function(val){ const rows = ".json_encode($rows)."; rows[{$rowIndex}]['{$fieldKey}'] = val; \$wire.setSetting('{$repeaterKey}', rows, false); })";
@endphp

<div class="space-y-1">
    @if ($fieldType !== 'toggle')
        <label for="{{ $fieldId }}" class="block text-[11px] font-medium text-ink-600">{{ $field['label'] }}</label>
    @endif

    @switch ($fieldType)
        @case('textarea')
            <textarea id="{{ $fieldId }}" rows="{{ $field['rows'] ?? 2 }}" class="field px-2 py-1.5 text-xs"
                      x-on:input.debounce.500ms="{!! $update !!}($event.target.value)">{{ is_scalar($fieldValue) ? $fieldValue : '' }}</textarea>
            @break

        @case('rich')
            <textarea id="{{ $fieldId }}" rows="4" class="field px-2 py-1.5 font-mono text-[11px]"
                      x-on:input.debounce.500ms="{!! $update !!}($event.target.value)">{{ is_scalar($fieldValue) ? $fieldValue : '' }}</textarea>
            <p class="text-[10px] text-ink-400">Basit HTML kullanabilirsiniz: &lt;p&gt;, &lt;strong&gt;, &lt;a&gt;</p>
            @break

        @case('select')
            <select id="{{ $fieldId }}" class="field px-2 py-1.5 text-xs"
                    x-on:change="{!! $update !!}($event.target.value)">
                @foreach ($field['options'] as $ov => $ol)
                    <option value="{{ $ov }}" @selected($fieldValue == $ov)>{{ $ol }}</option>
                @endforeach
            </select>
            @break

        @case('toggle')
            <label class="flex items-center gap-2 text-xs text-ink-700">
                <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600" @checked($fieldValue)
                       x-on:change="{!! $update !!}($event.target.checked)">
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
                        @click="$dispatch('open-media-picker', { repeater: '{{ $repeaterKey }}', row: {{ $rowIndex }}, field: '{{ $fieldKey }}', accept: 'image' })">
                    {{ $rowMedia ? 'Değiştir' : 'Görsel seç' }}
                </button>
                @if ($rowMedia)
                    <button type="button" class="btn-ghost btn-sm text-red-600" x-on:click="{!! $update !!}(null)">×</button>
                @endif
            </div>
            @break

        @case('link')
            <input id="{{ $fieldId }}" type="text" class="field px-2 py-1.5 text-xs" placeholder="https://…"
                   value="{{ is_array($fieldValue) ? ($fieldValue['url'] ?? '') : $fieldValue }}"
                   x-on:input.debounce.500ms="{!! $update !!}({ type: 'url', url: $event.target.value, target: '_self' })">
            @break

        @case('icon')
            <select id="{{ $fieldId }}" class="field px-2 py-1.5 text-xs"
                    x-on:change="{!! $update !!}($event.target.value)">
                @foreach (['' => 'Yok', 'check' => 'Onay', 'check-circle' => 'Onay (daire)', 'star' => 'Yıldız', 'arrow-right' => 'Sağ ok', 'phone' => 'Telefon', 'mail' => 'Zarf', 'map-pin' => 'Konum', 'shield' => 'Kalkan', 'award' => 'Ödül'] as $iv => $il)
                    <option value="{{ $iv }}" @selected($fieldValue === $iv)>{{ $il }}</option>
                @endforeach
            </select>
            @break

        @default
            <input id="{{ $fieldId }}" type="text" class="field px-2 py-1.5 text-xs"
                   value="{{ is_scalar($fieldValue) ? $fieldValue : '' }}"
                   x-on:input.debounce.500ms="{!! $update !!}($event.target.value)">
    @endswitch
</div>
