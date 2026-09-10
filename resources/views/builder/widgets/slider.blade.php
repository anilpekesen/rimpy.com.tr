@php $slides = $settings['slides'] ?? []; @endphp

@if ($slides)
    <div class="slider"
         data-slider
         data-autoplay="{{ ($settings['autoplay'] ?? true) ? '1' : '0' }}"
         data-interval="{{ (int) (data_get($settings, 'interval.size', 6) * 1000) }}"
         role="region" aria-roledescription="carousel" aria-label="Öne çıkanlar">

        @foreach ($slides as $index => $slide)
            @php
                $image = $context->media($slide['image'] ?? null);
                $link = $context->link($slide['link'] ?? null);
                $align = $slide['align'] ?? 'left';
            @endphp
            <div class="slide @if ($index === 0) is-active @endif"
                 role="group" aria-roledescription="slide"
                 aria-label="{{ $index + 1 }} / {{ count($slides) }}"
                 @if ($index !== 0) aria-hidden="true" @endif>

                @if ($image)
                    <img class="slide-bg @if ($settings['ken_burns'] ?? false) slide-bg--kb @endif"
                         src="{{ $image->url('large') }}" alt="{{ $image->altText() }}"
                         loading="{{ $index === 0 ? 'eager' : 'lazy' }}" decoding="async">
                @endif

                <div class="slide-overlay"></div>

                <div class="slide-inner" style="justify-content: {{ ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'][$align] }}">
                    <div class="slide-content" style="text-align: {{ $align }}; align-items: {{ ['left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end'][$align] }}">
                        @if (! empty($slide['eyebrow']))
                            <span class="hd-eyebrow">{{ $slide['eyebrow'] }}</span>
                        @endif
                        @if (! empty($slide['title']))
                            <h2 class="slide-title">{{ $slide['title'] }}</h2>
                        @endif
                        @if (! empty($slide['text']))
                            <p class="slide-text">{{ $slide['text'] }}</p>
                        @endif
                        @if (! empty($slide['button_text']) && ! empty($link['url']))
                            <a class="btn-el btn-el--solid btn-el--lg" href="{{ $link['url'] }}"
                               target="{{ $link['target'] }}" @if ($link['rel']) rel="{{ $link['rel'] }}" @endif>
                                {{ $slide['button_text'] }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        @if (($settings['arrows'] ?? true) && count($slides) > 1)
            <button class="slider-arrow slider-arrow--prev" data-slider-prev aria-label="Önceki slayt">
                <x-builder.icon name="chevron-right" style="transform:rotate(180deg)"/>
            </button>
            <button class="slider-arrow slider-arrow--next" data-slider-next aria-label="Sonraki slayt">
                <x-builder.icon name="chevron-right"/>
            </button>
        @endif

        @if (($settings['dots'] ?? true) && count($slides) > 1)
            <div class="slider-dots" role="tablist" aria-label="Slaytlar">
                @foreach ($slides as $index => $slide)
                    <button role="tab" data-slider-dot="{{ $index }}"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-label="{{ $index + 1 }}. slayda git"></button>
                @endforeach
            </div>
        @endif
    </div>
@endif
