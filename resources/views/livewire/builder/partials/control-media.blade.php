@php $media = is_numeric($value) ? \App\Models\Media::find($value) : null; @endphp

<div class="space-y-1.5" wire:key="{{ $selected['id'] }}-{{ $key }}-media">
    @if ($media)
        <div class="relative overflow-hidden rounded-lg border border-ink-200">
            @if ($media->isImage())
                <img src="{{ $media->url('small') }}" alt="{{ $media->altText() }}" class="h-28 w-full object-cover">
            @else
                <div class="flex h-28 items-center justify-center gap-2 bg-ink-50 text-ink-500">
                    <x-icon name="file-text" class="size-6"/>
                    <span class="text-xs">{{ strtoupper($media->extension) }}</span>
                </div>
            @endif
            <button type="button" x-on:click="$wire.setSetting('{{ $key }}', null, false)"
                    class="absolute top-1.5 right-1.5 rounded-md bg-white/95 px-2 py-0.5 text-xs font-medium text-red-600 shadow-sm hover:bg-white">
                Kaldır
            </button>
        </div>
        <p class="hint truncate">{{ $media->original_name }} · {{ $media->humanSize() }}</p>
        @if ($media->isImage() && blank($media->altText()))
            <p class="hint text-amber-700">
                Bu görselin alternatif metni yok. Medya bölümünden ekleyerek erişilebilirliği ve SEO'yu iyileştirebilirsiniz.
            </p>
        @endif
    @endif

    <button type="button" x-on:click="$dispatch('open-media-picker', { target: '{{ $key }}', accept: '{{ $control['accept'] ?? 'image' }}' })"
            x-on:media-applied.stop="$wire.setSetting('{{ $key }}', $event.detail.mediaId, false)"
            class="btn-secondary btn-sm w-full">
        <x-icon name="image" class="size-3.5"/> {{ $media ? 'Değiştir' : 'Dosya seç' }}
    </button>
</div>
