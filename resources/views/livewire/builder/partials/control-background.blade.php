@php
    $bg = is_array($value) ? $value : [];
    $bgType = $bg['type'] ?? 'color';
    $theme = app(\App\Support\Theme::class);
@endphp

<div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="{{ $selected['id'] }}-{{ $key }}-bg"
     x-data="{
        v: @js($bg + ['type' => 'color', 'color' => '', 'url' => '', 'position' => 'center center', 'size' => 'cover', 'repeat' => 'no-repeat', 'attachment' => '', 'from' => '#365ef8', 'to' => '#0f172a', 'angle' => 180]),
        push() { $wire.setSetting('{{ $key }}', this.v, false) }
     }">

    <div class="flex rounded-lg border border-ink-200 p-0.5">
        @foreach (['color' => 'Renk', 'gradient' => 'Geçiş', 'image' => 'Görsel'] as $option => $optionLabel)
            <button type="button" @click="v.type = '{{ $option }}'; push()"
                    class="flex-1 rounded-md px-2 py-1 text-xs font-medium transition"
                    :class="v.type === '{{ $option }}' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:bg-ink-100'">
                {{ $optionLabel }}
            </button>
        @endforeach
    </div>

    {{-- Düz renk --}}
    <div x-show="v.type === 'color'" class="space-y-1.5">
        <div class="flex flex-wrap gap-1">
            @foreach ($theme->get('colors', []) as $token => $hex)
                <button type="button" @click="v.color = @js('@'.$token); push()"
                        class="size-6 rounded-md border-2 border-ink-200 transition hover:border-ink-400"
                        :class="v.color === @js('@'.$token) && '!border-brand-600'"
                        style="background: {{ $hex }}" title="{{ $token }}">
                    <span class="sr-only">{{ $token }}</span>
                </button>
            @endforeach
        </div>
        <div class="flex items-center gap-2">
            <input type="color" class="size-8 shrink-0 cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                   x-model="v.color" @input.debounce.300ms="push()" aria-label="Zemin rengi">
            <input type="text" class="field flex-1 font-mono text-xs" x-model="v.color"
                   @input.debounce.400ms="push()" placeholder="#ffffff">
            <button type="button" class="btn-ghost btn-sm" @click="v.color = ''; push()" title="Temizle">×</button>
        </div>
    </div>

    {{-- Renk geçişi --}}
    <div x-show="v.type === 'gradient'" class="space-y-2">
        <div class="grid grid-cols-2 gap-2">
            <label class="block">
                <span class="mb-1 block text-[11px] text-ink-500">Başlangıç</span>
                <input type="color" class="h-8 w-full cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                       x-model="v.from" @input.debounce.300ms="push()">
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] text-ink-500">Bitiş</span>
                <input type="color" class="h-8 w-full cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                       x-model="v.to" @input.debounce.300ms="push()">
            </label>
        </div>
        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Yön (derece)</span>
            <input type="range" min="0" max="360" step="15" class="w-full accent-brand-600"
                   x-model.number="v.angle" @change="push()">
        </label>
    </div>

    {{-- Görsel --}}
    <div x-show="v.type === 'image'" class="space-y-2">
        <template x-if="v.url">
            <div class="relative overflow-hidden rounded-lg border border-ink-200">
                <img :src="v.url" alt="" class="h-24 w-full object-cover">
                <button type="button" @click="v.url = ''; push()"
                        class="absolute top-1 right-1 rounded-md bg-white/90 px-1.5 py-0.5 text-xs font-medium text-red-600 shadow-sm">
                    Kaldır
                </button>
            </div>
        </template>

        <button type="button" @click="$dispatch('open-media-picker', { target: '{{ $key }}.url', mode: 'url' })"
                @media-applied.stop="v.url = $event.detail.url; push()"
                class="btn-secondary btn-sm w-full">
            <x-icon name="image" class="size-3.5"/> <span x-text="v.url ? 'Görseli değiştir' : 'Görsel seç'"></span>
        </button>

        <div class="grid grid-cols-2 gap-2">
            <label class="block">
                <span class="mb-1 block text-[11px] text-ink-500">Konum</span>
                <select class="field px-1.5 py-1 text-xs" x-model="v.position" @change="push()">
                    <option value="center center">Ortada</option>
                    <option value="center top">Üstte</option>
                    <option value="center bottom">Altta</option>
                    <option value="left center">Solda</option>
                    <option value="right center">Sağda</option>
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] text-ink-500">Sığdırma</span>
                <select class="field px-1.5 py-1 text-xs" x-model="v.size" @change="push()">
                    <option value="cover">Alanı doldur</option>
                    <option value="contain">Tamamı görünsün</option>
                    <option value="auto">Gerçek boyut</option>
                </select>
            </label>
        </div>

        <label class="flex items-center gap-2 text-xs text-ink-700">
            <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600"
                   :checked="v.attachment === 'fixed'"
                   @change="v.attachment = $event.target.checked ? 'fixed' : ''; push()">
            Sayfa kayarken görsel sabit kalsın
        </label>

        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Görsel üstü renk (okunabilirlik için)</span>
            <input type="text" class="field font-mono text-xs" x-model="v.color"
                   @input.debounce.400ms="push()" placeholder="rgba(0,0,0,.4)">
        </label>
    </div>
</div>
