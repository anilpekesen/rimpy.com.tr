@php
    $media = $settings['image'] ?? null;
    $link = $settings['link'] ?? [];
    $hover = $settings['hover'] ?? '';
@endphp

@if ($media)
    <figure class="img-frame @if ($hover) img-frame--{{ $hover }} @endif">
        @if (! empty($link['url']))
            <a href="{{ $link['url'] }}" target="{{ $link['target'] }}" @if ($link['rel']) rel="{{ $link['rel'] }}" @endif>
        @elseif (! empty($settings['lightbox']))
            <a href="{{ $media->url() }}" data-lightbox>
        @endif

        <img src="{{ $media->url($settings['size'] ?? 'large') }}"
             @if ($srcset = $media->srcset()) srcset="{{ $srcset }}" sizes="(max-width: 768px) 100vw, 50vw" @endif
             alt="{{ $media->altText() }}"
             @if ($media->width) width="{{ $media->width }}" height="{{ $media->height }}" @endif
             loading="{{ ($settings['lazy'] ?? true) ? 'lazy' : 'eager' }}"
             decoding="async">

        @if (! empty($link['url']) || ! empty($settings['lightbox']))
            </a>
        @endif

        @if (! empty($settings['caption']))
            <figcaption class="img-caption">{{ $settings['caption'] }}</figcaption>
        @endif
    </figure>
@elseif ($context->editing)
    <div class="img-frame flex aspect-[4/3] items-center justify-center bg-ink-100 text-sm text-ink-400">
        Görsel seçilmedi
    </div>
@endif
