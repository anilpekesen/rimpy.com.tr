@php
    $q = is_array($value) ? $value : [];
    $types = \App\Models\ContentType::where('is_active', true)->orderBy('sort')->get();
    $selectedType = $types->firstWhere('key', $q['type'] ?? null);
    $terms = $selectedType
        ? \App\Models\Term::whereHas('taxonomy', fn ($t) => $t->where('content_type_id', $selectedType->id))
            ->with('translations', 'taxonomy')->get()
        : collect();
@endphp

<div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="{{ $selected['id'] }}-{{ $key }}-query">
    <label class="block">
        <span class="mb-1 block text-[11px] text-ink-500">İçerik türü</span>
        <select class="field px-1.5 py-1 text-xs"
                x-on:change="$wire.setSetting('{{ $key }}', Object.assign(@js($q), { type: $event.target.value, terms: [] }), false)">
            <option value="">— Seçin —</option>
            @foreach ($types as $t)
                <option value="{{ $t->key }}" @selected(($q['type'] ?? null) === $t->key)>{{ $t->pluralLabel('tr') }}</option>
            @endforeach
        </select>
    </label>

    @if ($terms->isNotEmpty())
        <div>
            <span class="mb-1 block text-[11px] text-ink-500">Yalnızca şu kategorilerden</span>
            <div class="max-h-32 space-y-1 overflow-y-auto rounded-md border border-ink-200 p-1.5">
                @foreach ($terms as $term)
                    <label class="flex items-center gap-1.5 text-xs text-ink-700">
                        <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600"
                               @checked(in_array($term->id, $q['terms'] ?? []))
                               x-on:change="
                                    const cur = @js(array_values($q['terms'] ?? []));
                                    const next = $event.target.checked ? [...cur, {{ $term->id }}] : cur.filter(i => i !== {{ $term->id }});
                                    $wire.setSetting('{{ $key }}', Object.assign(@js($q), { terms: next }), false);
                               ">
                        {{ $term->t('name') }}
                        <span class="text-ink-400">({{ $term->taxonomy->label('tr') }})</span>
                    </label>
                @endforeach
            </div>
            <p class="mt-1 text-[10px] text-ink-400">Hiçbiri seçilmezse tüm kategoriler gösterilir.</p>
        </div>
    @endif

    <div class="grid grid-cols-2 gap-2">
        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Kaç tane</span>
            <input type="number" min="1" max="48" class="field px-1.5 py-1 text-xs" value="{{ $q['limit'] ?? 6 }}"
                   x-on:input.debounce.400ms="$wire.setSetting('{{ $key }}', Object.assign(@js($q), { limit: Number($event.target.value) }), false)">
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Sıralama</span>
            <select class="field px-1.5 py-1 text-xs"
                    x-on:change="$wire.setSetting('{{ $key }}', Object.assign(@js($q), { order: $event.target.value }), false)">
                @foreach (['recent' => 'En yeniler önce', 'oldest' => 'En eskiler önce', 'manual' => 'Elle sıralama', 'popular' => 'En çok görüntülenen', 'random' => 'Rastgele'] as $ov => $ol)
                    <option value="{{ $ov }}" @selected(($q['order'] ?? 'recent') === $ov)>{{ $ol }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <label class="flex items-center gap-2 text-xs text-ink-700">
        <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600" @checked($q['featured_only'] ?? false)
               x-on:change="$wire.setSetting('{{ $key }}', Object.assign(@js($q), { featured_only: $event.target.checked }), false)">
        Yalnızca öne çıkarılanlar
    </label>

    <label class="flex items-center gap-2 text-xs text-ink-700">
        <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600" @checked($q['exclude_current'] ?? false)
               x-on:change="$wire.setSetting('{{ $key }}', Object.assign(@js($q), { exclude_current: $event.target.checked }), false)">
        Bulunulan içeriği listeden çıkar
    </label>
</div>
