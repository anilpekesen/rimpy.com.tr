@php
    $images = $settings['images'] ?? collect();
    $columns = $settings['columns'] ?? [];
    $cols = is_array($columns) ? ($columns['d']['size'] ?? 3) : 3;
    $layout = $settings['layout'] ?? 'grid';
@endphp

@if ($images->isNotEmpty())
    <div class="gallery gallery--{{ $layout }} @if ($settings['hover'] ?? null) gallery--{{ $settings['hover'] }} @endif"
         style="grid-template-columns: repeat({{ (int) $cols }}, minmax(0, 1fr))">
        @foreach ($images as $image)
            <figure class="gallery-item" @if ($ratio = $settings['ratio'] ?? null) style="aspect-ratio:{{ $ratio }}" @endif>
                @if ($settings['lightbox'] ?? true)
                    <a href="{{ $image->url('large') }}" data-lightbox="galeri-{{ $element->id }}">
                @endif
                <img src="{{ $image->url('medium') }}" alt="{{ $image->altText() }}" loading="lazy" decoding="async">
                @if ($settings['lightbox'] ?? true) </a> @endif

                @if (($settings['captions'] ?? false) && $caption = $image->altText())
                    <figcaption class="img-caption">{{ $caption }}</figcaption>
                @endif
            </figure>
        @endforeach
    </div>
@elseif ($context->editing)
    <p class="text-sm text-ink-400">Henüz görsel seçilmedi.</p>
@endif
