<div class="sr tone-dark" data-service-rail>
    <div class="sr-sticky">
        <div class="sr-head">
            @include('builder.widgets.partials.section-head')
            <span class="sr-progress" aria-hidden="true"><i data-progress></i></span>
        </div>
        <div class="sr-track" data-track>
            @foreach ($settings['items'] ?? [] as $item)
                @php $tag = $item['href']['url'] ? 'a' : 'article'; @endphp
                <{{ $tag }} class="sr-card @if ($loop->first) sr-card--accent @endif"
                    @if ($tag === 'a') href="{{ $item['href']['url'] }}" @endif>
                    @if ($item['image'])
                        <img class="sr-img" src="{{ $item['image']->url('medium') }}" alt="" loading="lazy">
                    @endif
                    <span class="sr-top"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span>{{ $item['tag'] ?? '' }}</span></span>
                    <span class="sr-body">
                        <span class="sr-title">{{ $item['title'] ?? '' }}</span>
                        <span class="sr-text">{{ $item['text'] ?? '' }}</span>
                    </span>
                </{{ $tag }}>
            @endforeach
        </div>
    </div>
</div>
