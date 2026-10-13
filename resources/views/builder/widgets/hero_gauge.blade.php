@php
    // Medya alanları widget tarafından zaten nesneye çevrilmiş gelir.
    $asMedia = fn ($value) => $value instanceof \App\Models\Media ? $value : $context->media($value);
    $video = $asMedia($settings['video'] ?? null);
    $poster = $asMedia($settings['poster'] ?? null);
    $link = $context->link($settings['primary_link'] ?? null);
    $rest = (float) data_get($settings, 'rest.size', 62) / 100;
    $dim = (float) data_get($settings, 'dim.size', 55) / 100;
    [$accent, $restOfLine] = array_pad(explode(' ', (string) ($settings['line2'] ?? ''), 2), 2, '');
@endphp

<div class="hg tone-dark @if ($video) hg--video @endif @if ($video && ! ($settings['gauge_with_video'] ?? false)) hg--no-gauge @endif" data-hero-gauge
     data-max="{{ (float) ($settings['max'] ?? 12000) }}"
     data-pressure="{{ (float) ($settings['pressure'] ?? 700) }}"
     data-rest="{{ $rest }}">
    @if ($video)
        <div class="hg-video" aria-hidden="true">
            <video autoplay muted loop playsinline preload="metadata" @if ($poster) poster="{{ $poster->url('large') }}" @endif>
                <source src="{{ $video->url() }}" type="{{ $video->mime }}">
            </video>
            <span class="hg-shade"></span>
            <span class="hg-dim" style="opacity:{{ $dim }}"></span>
        </div>
    @endif
    <div class="hg-grid" aria-hidden="true"></div>

    <div class="hg-inner">
        <div class="hg-copy">
            @if (filled($settings['eyebrow'] ?? null))<span class="sh-eyebrow">{{ $settings['eyebrow'] }}</span>@endif
            <h1 class="hg-title">
                <span class="hg-ln"><span>{{ $settings['line1'] ?? '' }}</span></span>
                <span class="hg-ln"><span><em>{{ $accent }}</em> {{ $restOfLine }}</span></span>
                <span class="hg-ln"><span class="hg-out">{{ $settings['line3'] ?? '' }}</span></span>
            </h1>
            @if (filled($settings['lead'] ?? null))<p class="hg-lead">{{ $settings['lead'] }}</p>@endif
            <div class="hg-cta">
                @if (filled($settings['primary_text'] ?? null))
                    <a class="xb xb--y" href="{{ $link['url'] ?? '#' }}" target="{{ $link['target'] ?? '_self' }}" @if ($link['rel'] ?? null) rel="{{ $link['rel'] }}" @endif>
                        {{ $settings['primary_text'] }} <x-icon name="arrow-right" />
                    </a>
                @endif
                @if (filled($settings['secondary_text'] ?? null))
                    <button type="button" class="xb xb--o" data-quote-open>{{ $settings['secondary_text'] }}</button>
                @endif
            </div>
            @if (! empty($settings['specs']))
                <dl class="hg-specs">
                    @foreach ($settings['specs'] as $spec)
                        <div><dd>{{ $spec['value'] ?? '' }}</dd><dt>{{ $spec['label'] ?? '' }}</dt></div>
                    @endforeach
                </dl>
            @endif
        </div>

        <div class="hg-gauge" aria-hidden="true">
            <svg viewBox="0 0 640 640">
                <defs>
                    <linearGradient id="hg-y-{{ $element->id }}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FFE066"/><stop offset="1" stop-color="var(--c-primary)"/></linearGradient>
                    <linearGradient id="hg-s-{{ $element->id }}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#3a3e46"/><stop offset=".5" stop-color="#1c1f24"/><stop offset="1" stop-color="#2c3037"/></linearGradient>
                </defs>
                <circle cx="320" cy="320" r="300" fill="none" stroke="rgba(255,255,255,.06)"/>
                <g data-ticks></g>
                <path data-track fill="none" stroke="rgba(255,255,255,.08)" stroke-width="14" stroke-linecap="round"/>
                <path data-arc fill="none" stroke="url(#hg-y-{{ $element->id }})" stroke-width="14" stroke-linecap="round" class="hg-arc"/>
                <g data-nut class="hg-nut">
                    <polygon points="320,110 502,215 502,425 320,530 138,425 138,215" fill="url(#hg-s-{{ $element->id }})" stroke="rgba(255,255,255,.18)"/>
                    <polygon points="320,140 476,230 476,410 320,500 164,410 164,230" fill="none" stroke="rgba(255,255,255,.07)"/>
                    <circle cx="320" cy="320" r="150" fill="#0d0e11" stroke="rgba(255,255,255,.12)"/>
                    <circle cx="320" cy="320" r="150" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="16" stroke-dasharray="2 10"/>
                    <circle cx="320" cy="170" r="5" fill="var(--c-primary)"/>
                </g>
            </svg>
            <div class="hg-read"><b data-value>0</b><small>{{ $settings['unit'] ?? 'N·m' }}</small></div>
            @if (filled($settings['tag_top'] ?? null))<span class="hg-tag hg-tag--top">{{ $settings['tag_top'] }}<i></i></span>@endif
            <span class="hg-tag hg-tag--right"><i></i><b data-bar>0</b>&nbsp;BAR</span>
            @if (filled($settings['tag_bottom'] ?? null))<span class="hg-tag hg-tag--bottom">{{ $settings['tag_bottom'] }}<i></i></span>@endif
            @if (filled($settings['hint'] ?? null))<span class="hg-hint">↕ {{ $settings['hint'] }}</span>@endif
        </div>
    </div>
</div>
