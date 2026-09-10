@php
    $link = $settings['link'] ?? [];
    $image = $settings['image'] ?? null;
@endphp

<div class="icon-box icon-box--{{ $settings['layout'] ?? 'top' }}">
    @if ($image)
        <span class="ib-icon"><img src="{{ $image->url('thumb') }}" alt="{{ $image->altText() }}" style="width:1em;height:1em;object-fit:contain"></span>
    @elseif (! empty($settings['icon']))
        <span class="ib-icon"><x-builder.icon :name="$settings['icon']"/></span>
    @endif

    <div class="ib-content">
        @if (! empty($settings['title']))
            <h3 class="ib-title">
                @if (! empty($link['url']))
                    <a href="{{ $link['url'] }}" target="{{ $link['target'] }}" @if ($link['rel']) rel="{{ $link['rel'] }}" @endif>{{ $settings['title'] }}</a>
                @else
                    {{ $settings['title'] }}
                @endif
            </h3>
        @endif
        @if (! empty($settings['text']))
            <p class="ib-text">{{ $settings['text'] }}</p>
        @endif
    </div>
</div>
