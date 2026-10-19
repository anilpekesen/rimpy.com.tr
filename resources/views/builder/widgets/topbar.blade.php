@php
    $on = fn (string $key) => $settings[$key] ?? true;
    $tel = preg_replace('/[^\d+]/', '', (string) $phone);
@endphp

<div class="tb">
    <ul class="tb-info">
        @if ($on('show_hours') && $hours)
            <li><x-builder.icon name="clock" /><span>{{ $hours }}</span></li>
        @endif
        @if ($on('show_address') && $address)
            <li class="tb-address"><x-builder.icon name="map-pin" /><span>{{ $address }}</span></li>
        @endif
        @if ($on('show_phone') && $phone)
            <li><x-builder.icon name="phone" /><a href="tel:{{ $tel }}">{{ $phone }}</a></li>
        @endif
        @if ($on('show_email') && $email)
            <li><x-builder.icon name="mail" /><a href="mailto:{{ $email }}">{{ $email }}</a></li>
        @endif
    </ul>

    @if ($on('show_social') && $social->isNotEmpty())
        <ul class="tb-social">
            @foreach ($social as $network)
                <li><a href="{{ $network['url'] }}" target="_blank" rel="noopener" aria-label="{{ $network['label'] }}"><x-builder.icon :name="$network['icon']" /></a></li>
            @endforeach
        </ul>
    @endif

    @if ($on('show_language'))
        <div class="tb-lang">
            @include('builder.widgets.language_switcher', ['settings' => ['display' => 'code', 'style' => 'inline']])
        </div>
    @endif
</div>
