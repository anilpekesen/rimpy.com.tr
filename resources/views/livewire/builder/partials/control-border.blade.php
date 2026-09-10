@php $b = is_array($value) ? $value : []; @endphp

<div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="{{ $selected['id'] }}-{{ $key }}-border"
     x-data="{
        v: @js($b + ['style' => 'none', 'color' => '', 'width' => ['t'=>'','r'=>'','b'=>'','l'=>'','unit'=>'px'], 'radius' => ['t'=>'','r'=>'','b'=>'','l'=>'','unit'=>'px']]),
        push() { $wire.setSetting('{{ $key }}', this.v, false) }
     }">

    <label class="block">
        <span class="mb-1 block text-[11px] text-ink-500">Çizgi biçimi</span>
        <select class="field px-1.5 py-1 text-xs" x-model="v.style" @change="push()">
            <option value="none">Kenarlık yok</option>
            <option value="solid">Düz</option>
            <option value="dashed">Kesikli</option>
            <option value="dotted">Noktalı</option>
        </select>
    </label>

    <div x-show="v.style !== 'none'" class="space-y-2">
        <div>
            <span class="mb-1 block text-[11px] text-ink-500">Kalınlık</span>
            <div class="flex gap-1.5">
                @foreach (['t' => 'Üst', 'r' => 'Sağ', 'b' => 'Alt', 'l' => 'Sol'] as $side => $sideLabel)
                    <div class="flex-1">
                        <input type="number" class="field px-1 py-1 text-center text-xs" placeholder="0"
                               x-model="v.width.{{ $side }}" @input.debounce.400ms="push()" aria-label="Kalınlık {{ $sideLabel }}">
                        <span class="mt-0.5 block text-center text-[10px] text-ink-400">{{ $sideLabel }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-[11px] text-ink-500">Renk</span>
            <input type="color" class="size-7 shrink-0 cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                   x-model="v.color" @input.debounce.300ms="push()" aria-label="Kenarlık rengi">
            <input type="text" class="field flex-1 font-mono text-xs" x-model="v.color" @input.debounce.400ms="push()">
        </div>
    </div>

    <div>
        <span class="mb-1 block text-[11px] text-ink-500">Köşe yuvarlaklığı</span>
        <div class="flex gap-1.5">
            @foreach (['t' => 'Sol üst', 'r' => 'Sağ üst', 'b' => 'Sağ alt', 'l' => 'Sol alt'] as $corner => $cornerLabel)
                <div class="flex-1">
                    <input type="number" class="field px-1 py-1 text-center text-xs" placeholder="0"
                           x-model="v.radius.{{ $corner }}" @input.debounce.400ms="push()" aria-label="{{ $cornerLabel }}">
                    <span class="mt-0.5 block text-center text-[10px] text-ink-400">{{ $cornerLabel }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
