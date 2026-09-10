@php $link = $settings['link'] ?? []; @endphp

@if (! empty($settings['eyebrow']))
    <span class="hd-eyebrow">{{ $settings['eyebrow'] }}</span>
@endif

@if (! empty($link['url']))
    <a href="{{ $link['url'] }}" target="{{ $link['target'] }}" @if ($link['rel']) rel="{{ $link['rel'] }}" @endif class="no-underline">
@endif

<{{ $tag = in_array($settings['tag'] ?? 'h2', ['h1','h2','h3','h4','h5','h6','div'], true) ? $settings['tag'] : 'h2' }} class="hd">
    {{ $settings['text'] ?? '' }}
</{{ $tag }}>

@if (! empty($link['url']))
    </a>
@endif
