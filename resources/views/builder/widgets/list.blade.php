<ul class="list @if (! empty($settings['divider'])) list--divided @endif">
    @foreach ($settings['items'] ?? [] as $item)
        @php $link = $context->link($item['link'] ?? null); @endphp
        <li class="list-row">
            @if (! empty($item['icon']))
                <span class="list-icon"><x-builder.icon :name="$item['icon']"/></span>
            @endif
            <span class="list-text">
                @if (! empty($link['url']))
                    <a href="{{ $link['url'] }}" target="{{ $link['target'] }}" @if ($link['rel']) rel="{{ $link['rel'] }}" @endif>{{ $item['text'] ?? '' }}</a>
                @else
                    {{ $item['text'] ?? '' }}
                @endif
            </span>
        </li>
    @endforeach
</ul>
