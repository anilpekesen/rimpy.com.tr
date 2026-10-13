<div class="st" data-reveal>
    @include('builder.widgets.partials.section-head')
    <ol class="st-list">
        @foreach ($settings['items'] ?? [] as $item)
            <li class="st-item" style="--i: {{ $loop->index }}">
                <i class="st-bar" aria-hidden="true"></i>
                <span class="st-n">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="st-title">{{ $item['title'] ?? '' }}</h3>
                <p class="st-text">{{ $item['text'] ?? '' }}</p>
            </li>
        @endforeach
    </ol>
</div>
