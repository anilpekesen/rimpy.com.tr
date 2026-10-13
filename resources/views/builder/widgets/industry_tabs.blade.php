@php $items = $settings['items'] ?? []; @endphp

<div class="it" data-industry-tabs>
    @include('builder.widgets.partials.section-head')
    <div class="it-wrap">
        <div class="it-list" role="tablist" aria-orientation="vertical">
            @foreach ($items as $item)
                <button type="button" role="tab" id="it-{{ $element->id }}-{{ $loop->index }}"
                        aria-controls="itp-{{ $element->id }}-{{ $loop->index }}"
                        aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? 0 : -1 }}">
                    <span class="it-i">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="it-t">{{ $item['title'] ?? '' }}</span>
                    <x-icon name="arrow-right" class="it-a" />
                </button>
            @endforeach
        </div>
        <div class="it-panels">
            @foreach ($items as $item)
                <div class="it-panel" role="tabpanel" id="itp-{{ $element->id }}-{{ $loop->index }}"
                     aria-labelledby="it-{{ $element->id }}-{{ $loop->index }}" @unless ($loop->first) hidden @endunless>
                    @if ($item['image'])<img class="it-img" src="{{ $item['image']->url('large') }}" alt="" loading="lazy">@endif
                    <div class="it-content">
                        <span class="sh-eyebrow">{{ $context->locale === 'tr' ? 'Önerilen set' : 'Recommended kit' }}</span>
                        <h3 class="it-title">{{ $item['title'] ?? '' }}</h3>
                        <p class="it-text">{{ $item['text'] ?? '' }}</p>
                        @if ($item['list'])
                            <ul class="it-points">@foreach ($item['list'] as $point)<li>{{ $point }}</li>@endforeach</ul>
                        @endif
                        @if ($item['href']['url'])
                            <a class="it-link" href="{{ $item['href']['url'] }}">{{ $context->locale === 'tr' ? 'Sektör sayfası' : 'Industry page' }} <x-icon name="arrow-right" /></a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
