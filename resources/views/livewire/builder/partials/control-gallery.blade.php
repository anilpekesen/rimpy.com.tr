@php
    $ids = is_array($value) ? $value : [];
    $items = $ids ? \App\Models\Media::whereIn('id', $ids)->get()->keyBy('id') : collect();
@endphp

<div class="space-y-2" wire:key="{{ $selected['id'] }}-{{ $key }}-gallery">
    @if ($ids)
        <ul class="grid grid-cols-4 gap-1.5">
            @foreach ($ids as $index => $id)
                @php $media = $items->get($id); @endphp
                @continue (! $media)
                <li class="group relative aspect-square overflow-hidden rounded-md border border-ink-200">
                    <img src="{{ $media->url('thumb') }}" alt="{{ $media->altText() }}" class="size-full object-cover">
                    <button type="button"
                            x-on:click="$wire.setSetting('{{ $key }}', @js(array_values(array_diff($ids, [$id]))), false)"
                            class="absolute inset-0 flex items-center justify-center bg-ink-950/60 text-xs font-medium text-white opacity-0 transition group-hover:opacity-100"
                            title="Kaldır">
                        Kaldır
                    </button>
                </li>
            @endforeach
        </ul>
        <p class="hint">{{ count($ids) }} görsel seçili. Sıralamayı değiştirmek için kaldırıp yeniden ekleyin.</p>
    @endif

    <button type="button" x-on:click="$dispatch('open-media-picker', { target: '{{ $key }}', multiple: true, existing: @js($ids) })"
            x-on:media-applied.stop="$wire.setSetting('{{ $key }}', [...new Set([...@js($ids), ...$event.detail.mediaIds])], false)"
            class="btn-secondary btn-sm w-full">
        <x-icon name="image" class="size-3.5"/> {{ $ids ? 'Görsel ekle' : 'Görselleri seç' }}
    </button>
</div>
