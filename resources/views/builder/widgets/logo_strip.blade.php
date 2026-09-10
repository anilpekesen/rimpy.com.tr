@php
    $logos = $settings['logos'] ?? collect();
    $cols = data_get($settings, 'columns.d.size', 5);
    $marquee = ($settings['layout'] ?? 'grid') === 'marquee';
@endphp

@if ($logos->isNotEmpty())
    <div class="logo-strip @if ($settings['grayscale'] ?? true) logo-strip--gray @endif @if ($marquee) logo-strip--marquee @endif"
         @if ($marquee)
             style="--marquee-speed: {{ (int) data_get($settings, 'speed.size', 35) }}s"
         @else
             style="grid-template-columns: repeat({{ (int) $cols }}, minmax(0, 1fr))"
         @endif>
        @foreach ($logos as $logo)
            <div class="logo-item">
                <img src="{{ $logo->url('small') }}" alt="{{ $logo->altText() }}" loading="lazy" decoding="async">
            </div>
        @endforeach

        @if ($marquee)
            {{-- Kesintisiz kayma için liste bir kez daha basılır --}}
            @foreach ($logos as $logo)
                <div class="logo-item" aria-hidden="true">
                    <img src="{{ $logo->url('small') }}" alt="" loading="lazy">
                </div>
            @endforeach
        @endif
    </div>
@endif
