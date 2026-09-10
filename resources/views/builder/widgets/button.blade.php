@php
    $link = $settings['link'] ?? [];
    $classes = collect([
        'btn-el',
        'btn-el--'.($settings['variant'] ?? 'solid'),
        'btn-el--'.($settings['size'] ?? 'md'),
        ! empty($settings['full_width']) ? 'btn-el--full' : null,

    ])->filter()->implode(' ');
    $tag = ! empty($link['url']) ? 'a' : 'span';
@endphp

<{{ $tag }} class="{{ $classes }}"
    @if ($tag === 'a')
        href="{{ $link['url'] }}" target="{{ $link['target'] }}" @if ($link['rel']) rel="{{ $link['rel'] }}" @endif
    @endif>
    @if (($settings['icon_position'] ?? 'right') === 'left')
        <x-builder.icon :name="$settings['icon'] ?? ''"/>
    @endif
    {{ $settings['text'] ?? '' }}
    @if (($settings['icon_position'] ?? 'right') === 'right')
        <x-builder.icon :name="$settings['icon'] ?? ''"/>
    @endif
</{{ $tag }}>
