@php
/**
 * Tek bir ayarın arayüzünü çizer.
 *
 * Denetimin türü tanımında yazdığı için burada yalnızca doğru girdi
 * seçilir. Yeni bir widget yazan kişinin ayrıca arayüz kodu yazması
 * gerekmez.
 */
$type = $control['type'] ?? 'text';
$responsive = ! empty($control['responsive']);
$value = $this->settingValue($key, $responsive, $control['default'] ?? null);
$id = 'ctrl-'.$key;

// Koşullu denetim: bağlı olduğu ayar beklenen değerde değilse hiç çizilmez.
if ($condition = $control['condition'] ?? null) {
    foreach ($condition as $dependsOn => $expected) {
        $actual = $this->settingValue($dependsOn, false);
        $matches = is_array($expected) ? in_array($actual, $expected, true) : $actual == $expected;

        if (! $matches) {
            return;
        }
    }
}

$devices = ['d' => 'Masaüstü', 't' => 'Tablet', 'm' => 'Telefon'];
@endphp

<div class="space-y-1.5">
    @if (! in_array($type, ['toggle', 'notice'], true))
        <div class="flex items-baseline justify-between gap-2">
            <label for="{{ $id }}" class="label mb-0">{{ $control['label'] }}</label>
            @if ($responsive)
                <span class="text-[10px] font-medium text-ink-400" title="Bu ayar her cihaz için ayrı tutulur">
                    {{ $devices[$device] }}
                </span>
            @endif
        </div>
    @endif

    @switch ($type)

        {{-- ── Bilgi kutusu (değer tutmaz) ───────────────────────────── --}}
        @case('notice')
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-2.5 text-xs leading-relaxed text-amber-900">
                <p>{{ $control['text'] }}</p>
                @if (! empty($control['route']) && Route::has($control['route']) && auth()->user()?->atLeast('admin'))
                    <a href="{{ route($control['route']) }}" target="_blank" class="mt-1.5 inline-flex items-center gap-1 font-semibold underline underline-offset-2">
                        {{ $control['link_label'] ?? 'Aç' }} ↗
                    </a>
                @endif
            </div>
            @break

        {{-- ── Metin ──────────────────────────────────────────────────── --}}
        @case('text')
            <input id="{{ $id }}" type="text" class="field"
                   value="{{ is_scalar($value) ? $value : '' }}"
                   wire:key="{{ $selected['id'] }}-{{ $key }}"
                   x-on:input.debounce.400ms="$wire.setSetting('{{ $key }}', $event.target.value, {{ $responsive ? 'true' : 'false' }})">
            @break

        @case('textarea')
            <textarea id="{{ $id }}" rows="{{ $control['rows'] ?? 3 }}"
                      class="field {{ ! empty($control['mono']) ? 'font-mono text-xs' : '' }}"
                      wire:key="{{ $selected['id'] }}-{{ $key }}"
                      x-on:input.debounce.400ms="$wire.setSetting('{{ $key }}', $event.target.value, {{ $responsive ? 'true' : 'false' }})">{{ is_scalar($value) ? $value : '' }}</textarea>
            @break

        @case('rich')
            <div wire:ignore wire:key="{{ $selected['id'] }}-{{ $key }}">
                <div class="overflow-hidden rounded-lg border border-ink-300">
                    <div class="flex flex-wrap gap-0.5 border-b border-ink-200 bg-ink-50 p-1"
                         x-data="{ cmd(c, v = null) { document.execCommand(c, false, v); $refs.editor.focus(); } }">
                        <button type="button" @click="cmd('bold')" class="btn-ghost btn-sm font-bold" title="Kalın">B</button>
                        <button type="button" @click="cmd('italic')" class="btn-ghost btn-sm italic" title="İtalik">I</button>
                        <button type="button" @click="cmd('formatBlock', '<h2>')" class="btn-ghost btn-sm" title="Başlık">H2</button>
                        <button type="button" @click="cmd('formatBlock', '<h3>')" class="btn-ghost btn-sm" title="Alt başlık">H3</button>
                        <button type="button" @click="cmd('formatBlock', '<p>')" class="btn-ghost btn-sm" title="Paragraf">P</button>
                        <button type="button" @click="cmd('insertUnorderedList')" class="btn-ghost btn-sm" title="Madde listesi">•</button>
                        <button type="button" @click="cmd('insertOrderedList')" class="btn-ghost btn-sm" title="Numaralı liste">1.</button>
                        <button type="button" @click="const u = prompt('Bağlantı adresi:'); if (u) cmd('createLink', u)" class="btn-ghost btn-sm" title="Bağlantı">
                            <x-icon name="link" class="size-3.5"/>
                        </button>
                        <button type="button" @click="cmd('removeFormat')" class="btn-ghost btn-sm ml-auto" title="Biçimi temizle">⌫</button>
                    </div>
                    <div x-ref="editor" contenteditable="true"
                         class="prose prose-sm max-w-none px-3 py-2 text-sm focus:outline-none [&_h2]:text-base [&_h2]:font-semibold [&_h3]:text-sm [&_h3]:font-semibold [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5"
                         style="min-height: 120px"
                         x-on:input.debounce.500ms="$wire.setSetting('{{ $key }}', $refs.editor.innerHTML, {{ $responsive ? 'true' : 'false' }})"
                    >{!! is_scalar($value) ? $value : '' !!}</div>
                </div>
            </div>
            @break

        @case('number')
            <input id="{{ $id }}" type="number" class="field"
                   value="{{ is_scalar($value) ? $value : '' }}"
                   wire:key="{{ $selected['id'] }}-{{ $key }}"
                   x-on:input.debounce.400ms="$wire.setSetting('{{ $key }}', $event.target.value === '' ? null : Number($event.target.value), {{ $responsive ? 'true' : 'false' }})">
            @break

        {{-- ── Kaydırıcı ──────────────────────────────────────────────── --}}
        @case('slider')
            @php
                $units = $control['units'] ?? [$control['unit'] ?? 'px'];
                $size = is_array($value) ? ($value['size'] ?? '') : ($value ?? '');
                $unit = is_array($value) ? ($value['unit'] ?? $units[0]) : $units[0];
            @endphp
            {{-- push(), kaydırıcı ve sayı kutusu hangisi değişirse değişsin tek bir ayar yazar --}}
            <div class="flex items-center gap-2" wire:key="{{ $selected['id'] }}-{{ $key }}"
                 x-data="{
                    size: @js($size), unit: @js($unit),
                    push() { $wire.setSetting('{{ $key }}', { size: this.size === '' ? null : Number(this.size), unit: this.unit }, {{ $responsive ? 'true' : 'false' }}) }
                 }">
                <input type="range" class="flex-1 accent-brand-600"
                       min="{{ $control['min'] ?? 0 }}" max="{{ $control['max'] ?? 200 }}" step="{{ $control['step'] ?? 1 }}"
                       x-model.number="size" @change="push()">
                <input type="number" class="field w-16 px-1.5 py-1 text-center text-xs"
                       x-model.number="size" @input.debounce.400ms="push()" aria-label="{{ $control['label'] }} değeri">
                @if (count($units) > 1)
                    <select class="field w-14 px-1 py-1 text-xs" x-model="unit" @change="push()" aria-label="Birim">
                        @foreach ($units as $u) <option value="{{ $u }}">{{ $u }}</option> @endforeach
                    </select>
                @else
                    <span class="w-8 text-xs text-ink-400">{{ $units[0] }}</span>
                @endif
            </div>
            @break

        {{-- ── Seçim ──────────────────────────────────────────────────── --}}
        @case('select')
            <select id="{{ $id }}" class="field" wire:key="{{ $selected['id'] }}-{{ $key }}"
                    x-on:change="$wire.setSetting('{{ $key }}', $event.target.value, {{ $responsive ? 'true' : 'false' }})">
                @foreach ($control['options'] as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected($value == $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            @break

        @case('choose')
            <div class="flex rounded-lg border border-ink-200 p-0.5" role="group" aria-label="{{ $control['label'] }}"
                 wire:key="{{ $selected['id'] }}-{{ $key }}">
                @foreach ($control['options'] as $optionValue => $option)
                    <button type="button"
                            x-on:click="$wire.setSetting('{{ $key }}', '{{ $optionValue }}', {{ $responsive ? 'true' : 'false' }})"
                            title="{{ $option['label'] }}"
                            class="flex-1 rounded-md px-2 py-1.5 text-xs font-medium transition
                                   {{ $value === $optionValue ? 'bg-ink-900 text-white' : 'text-ink-500 hover:bg-ink-100' }}">
                        {{ $option['label'] }}
                    </button>
                @endforeach
            </div>
            @break

        @case('toggle')
            <label class="flex cursor-pointer items-start gap-2.5" wire:key="{{ $selected['id'] }}-{{ $key }}">
                <input type="checkbox" class="mt-0.5 size-4 shrink-0 rounded border-ink-300 text-brand-600 focus:ring-brand-500"
                       @checked($value)
                       x-on:change="$wire.setSetting('{{ $key }}', $event.target.checked, {{ $responsive ? 'true' : 'false' }})">
                <span class="text-sm text-ink-700">{{ $control['label'] }}</span>
            </label>
            @break

        {{-- ── Renk ───────────────────────────────────────────────────── --}}
        @case('color')
            @php $theme = app(\App\Support\Theme::class); @endphp
            <div class="space-y-1.5" wire:key="{{ $selected['id'] }}-{{ $key }}">
                <div class="flex flex-wrap gap-1">
                    @foreach ($theme->get('colors', []) as $token => $hex)
                        <button type="button" title="Tema rengi: {{ $token }}"
                                x-on:click="$wire.setSetting('{{ $key }}', @js('@'.$token), {{ $responsive ? 'true' : 'false' }})"
                                class="size-6 rounded-md border-2 transition
                                       {{ $value === '@'.$token ? 'border-brand-600' : 'border-ink-200 hover:border-ink-400' }}"
                                style="background: {{ $hex }}">
                            <span class="sr-only">{{ $token }}</span>
                        </button>
                    @endforeach
                </div>
                <div class="flex items-center gap-2">
                    <input type="color" class="size-8 shrink-0 cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                           value="{{ str_starts_with((string) $value, '#') ? $value : '#000000' }}"
                           x-on:input.debounce.300ms="$wire.setSetting('{{ $key }}', $event.target.value, {{ $responsive ? 'true' : 'false' }})"
                           aria-label="Özel renk">
                    <input type="text" class="field flex-1 font-mono text-xs" placeholder="Tema rengi ya da #ffffff"
                           value="{{ is_scalar($value) ? $value : '' }}"
                           x-on:input.debounce.400ms="$wire.setSetting('{{ $key }}', $event.target.value, {{ $responsive ? 'true' : 'false' }})">
                    @if ($value)
                        <button type="button" class="btn-ghost btn-sm" title="Temizle"
                                x-on:click="$wire.setSetting('{{ $key }}', null, {{ $responsive ? 'true' : 'false' }})">×</button>
                    @endif
                </div>
                @if (str_starts_with((string) $value, '@'))
                    <p class="hint">Tema rengi seçildi — paleti değiştirdiğinizde burası da değişir.</p>
                @endif
            </div>
            @break

        {{-- ── Kenar boşlukları ───────────────────────────────────────── --}}
        @case('dimensions')
            @php
                $dims = is_array($value) ? $value : [];
                $dimUnits = $control['units'] ?? ['px'];
            @endphp
            <div wire:key="{{ $selected['id'] }}-{{ $key }}"
                 x-data="{
                    v: @js(['t' => $dims['t'] ?? '', 'r' => $dims['r'] ?? '', 'b' => $dims['b'] ?? '', 'l' => $dims['l'] ?? '', 'unit' => $dims['unit'] ?? $dimUnits[0]]),
                    linked: {{ (($dims['t'] ?? null) === ($dims['r'] ?? null) && ($dims['r'] ?? null) === ($dims['b'] ?? null) && ($dims['b'] ?? null) === ($dims['l'] ?? null)) ? 'true' : 'false' }},
                    set(side, val) {
                        if (this.linked) { this.v.t = this.v.r = this.v.b = this.v.l = val; }
                        else { this.v[side] = val; }
                        this.push();
                    },
                    push() {
                        const out = { unit: this.v.unit };
                        for (const s of ['t','r','b','l']) out[s] = this.v[s] === '' ? null : Number(this.v[s]);
                        $wire.setSetting('{{ $key }}', out, {{ $responsive ? 'true' : 'false' }});
                    }
                 }">
                <div class="flex items-center gap-1.5">
                    @foreach (['t' => 'Üst', 'r' => 'Sağ', 'b' => 'Alt', 'l' => 'Sol'] as $side => $sideLabel)
                        <div class="flex-1">
                            <input type="number" class="field px-1 py-1 text-center text-xs" placeholder="—"
                                   x-model="v.{{ $side }}" @input.debounce.400ms="set('{{ $side }}', v.{{ $side }})"
                                   aria-label="{{ $control['label'] }} {{ $sideLabel }}">
                            <span class="mt-0.5 block text-center text-[10px] text-ink-400">{{ $sideLabel }}</span>
                        </div>
                    @endforeach

                    <button type="button" @click="linked = !linked" class="btn-ghost btn-sm shrink-0 self-start"
                            :title="linked ? 'Kenarları ayrı ayarla' : 'Tüm kenarları birlikte ayarla'"
                            :class="linked && 'text-brand-600'">
                        <x-icon name="link" class="size-3.5"/>
                    </button>

                    @if (count($dimUnits) > 1)
                        <select class="field w-13 shrink-0 self-start px-1 py-1 text-xs" x-model="v.unit" @change="push()" aria-label="Birim">
                            @foreach ($dimUnits as $u) <option value="{{ $u }}">{{ $u }}</option> @endforeach
                        </select>
                    @endif
                </div>
            </div>
            @break

        {{-- ── Tipografi ──────────────────────────────────────────────── --}}
        @case('typography')
            @php $typo = is_array($value) ? $value : []; @endphp
            <div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="{{ $selected['id'] }}-{{ $key }}"
                 x-data="{
                    v: @js($typo + ['family' => '', 'size' => '', 'weight' => '', 'lh' => '', 'ls' => '', 'transform' => '']),
                    push() {
                        const out = {};
                        for (const [k, val] of Object.entries(this.v)) if (val !== '' && val !== null) out[k] = val;
                        $wire.setSetting('{{ $key }}', out, {{ $responsive ? 'true' : 'false' }});
                    }
                 }">
                <div class="grid grid-cols-2 gap-2">
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Yazı tipi</span>
                        <select class="field px-1.5 py-1 text-xs" x-model="v.family" @change="push()">
                            <option value="">Tema</option>
                            <option value="heading">Başlık yazı tipi</option>
                            <option value="body">Metin yazı tipi</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Boyut (px)</span>
                        <input type="number" class="field px-1.5 py-1 text-xs" x-model="v.size" @input.debounce.400ms="push()">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Kalınlık</span>
                        <select class="field px-1.5 py-1 text-xs" x-model="v.weight" @change="push()">
                            <option value="">Varsayılan</option>
                            <option value="300">İnce</option>
                            <option value="400">Normal</option>
                            <option value="500">Orta</option>
                            <option value="600">Yarı kalın</option>
                            <option value="700">Kalın</option>
                            <option value="800">Çok kalın</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Satır yüksekliği</span>
                        <input type="number" step="0.05" class="field px-1.5 py-1 text-xs" x-model="v.lh" @input.debounce.400ms="push()" placeholder="1.4">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Harf aralığı (em)</span>
                        <input type="number" step="0.01" class="field px-1.5 py-1 text-xs" x-model="v.ls" @input.debounce.400ms="push()" placeholder="0">
                    </label>
                    <label class="block">
                        <span class="mb-1 block text-[11px] text-ink-500">Büyük/küçük</span>
                        <select class="field px-1.5 py-1 text-xs" x-model="v.transform" @change="push()">
                            <option value="">Yazıldığı gibi</option>
                            <option value="uppercase">BÜYÜK HARF</option>
                            <option value="lowercase">küçük harf</option>
                            <option value="capitalize">Baş Harfler</option>
                        </select>
                    </label>
                </div>
            </div>
            @break

        {{-- ── Arka plan ──────────────────────────────────────────────── --}}
        @case('background')
            @include('livewire.builder.partials.control-background')
            @break

        @case('border')
            @include('livewire.builder.partials.control-border')
            @break

        @case('shadow')
            @include('livewire.builder.partials.control-shadow')
            @break

        {{-- ── Medya ──────────────────────────────────────────────────── --}}
        @case('media')
            @include('livewire.builder.partials.control-media')
            @break

        @case('gallery')
            @include('livewire.builder.partials.control-gallery')
            @break

        @case('link')
            @include('livewire.builder.partials.control-link')
            @break

        @case('icon')
            @include('livewire.builder.partials.control-icon')
            @break

        @case('repeater')
            @include('livewire.builder.partials.control-repeater')
            @break

        @case('query')
            @include('livewire.builder.partials.control-query')
            @break

        {{-- ── Kısa listeler ──────────────────────────────────────────── --}}
        @case('form_select')
        @case('menu_select')
        @case('template_select')
        @case('slider_select')
            @php
                $options = match ($type) {
                    'form_select' => \App\Models\Form::orderBy('name')->pluck('name', 'id'),
                    'menu_select' => \App\Models\Menu::orderBy('name')->pluck('name', 'key'),
                    'slider_select' => \App\Models\Slider::where('is_active', true)->orderBy('name')->pluck('name', 'id'),
                    default => \App\Models\Template::where('type', $control['template_type'] ?? 'section')->orderBy('name')->pluck('name', 'id'),
                };
            @endphp
            <select id="{{ $id }}" class="field" wire:key="{{ $selected['id'] }}-{{ $key }}"
                    x-on:change="$wire.setSetting('{{ $key }}', $event.target.value || null, false)">
                <option value="">— Seçin —</option>
                @foreach ($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}" @selected($value == $optionValue)>{{ $optionLabel }}</option>
                @endforeach
            </select>
            @if ($options->isEmpty())
                <p class="hint text-amber-700">Henüz tanımlı bir kayıt yok. Önce ilgili bölümden oluşturun.</p>
            @endif
            @break

        {{-- ── Cihaz görünürlüğü ──────────────────────────────────────── --}}
        @case('device_visibility')
            @php $hide = is_array($value) ? $value : []; @endphp
            <div class="space-y-1.5" wire:key="{{ $selected['id'] }}-{{ $key }}">
                @foreach ($devices as $deviceKey => $deviceLabel)
                    <label class="flex items-center gap-2 text-sm text-ink-700">
                        <input type="checkbox" class="size-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500"
                               @checked($hide[$deviceKey] ?? false)
                               x-on:change="$wire.setSetting('{{ $key }}', Object.assign(@js($hide), { '{{ $deviceKey }}': $event.target.checked }), false)">
                        {{ $deviceLabel }} cihazlarda gizle
                    </label>
                @endforeach
            </div>
            @break

        {{-- ── Sütun genişliği ────────────────────────────────────────── --}}
        @case('column_width')
            <div class="flex items-center gap-2" wire:key="{{ $selected['id'] }}-{{ $key }}">
                <input type="range" min="10" max="100" step="1" class="flex-1 accent-brand-600"
                       value="{{ is_numeric($value) ? $value : 100 }}"
                       x-on:change="$wire.setSetting('{{ $key }}', Number($event.target.value), true)">
                <span class="w-12 text-right text-xs text-ink-500">{{ is_numeric($value) ? round($value) : 100 }}%</span>
            </div>
            <p class="hint">Aynı bölümdeki sütunların toplamı %100'ü aşarsa alt satıra kayarlar.</p>
            @break

        @default
            <input id="{{ $id }}" type="text" class="field" value="{{ is_scalar($value) ? $value : '' }}"
                   wire:key="{{ $selected['id'] }}-{{ $key }}"
                   x-on:input.debounce.400ms="$wire.setSetting('{{ $key }}', $event.target.value, {{ $responsive ? 'true' : 'false' }})">
    @endswitch

    @if (! empty($control['hint']) && $type !== 'color')
        <p class="hint">{{ $control['hint'] }}</p>
    @endif
</div>
