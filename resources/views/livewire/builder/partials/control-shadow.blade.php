@php $sh = is_array($value) ? $value : []; @endphp

<div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="{{ $selected['id'] }}-{{ $key }}-shadow"
     x-data="{
        v: @js($sh + ['enabled' => false, 'x' => 0, 'y' => 8, 'blur' => 24, 'spread' => 0, 'color' => 'rgba(0,0,0,0.12)']),
        push() { $wire.setSetting('{{ $key }}', this.v.enabled ? this.v : null, false) }
     }">

    <label class="flex items-center gap-2 text-sm text-ink-700">
        <input type="checkbox" class="size-4 rounded border-ink-300 text-brand-600" x-model="v.enabled" @change="push()">
        Gölge kullan
    </label>

    <div x-show="v.enabled" class="space-y-2">
        <div class="grid grid-cols-2 gap-2">
            @foreach (['x' => 'Yatay', 'y' => 'Dikey', 'blur' => 'Yumuşaklık', 'spread' => 'Yayılım'] as $field => $fieldLabel)
                <label class="block">
                    <span class="mb-1 block text-[11px] text-ink-500">{{ $fieldLabel }}</span>
                    <input type="number" class="field px-1.5 py-1 text-xs"
                           x-model.number="v.{{ $field }}" @input.debounce.400ms="push()">
                </label>
            @endforeach
        </div>
        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Renk</span>
            <input type="text" class="field font-mono text-xs" x-model="v.color" @input.debounce.400ms="push()">
        </label>
    </div>
</div>
