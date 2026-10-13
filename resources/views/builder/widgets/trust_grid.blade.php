<div class="tg">
    @include('builder.widgets.partials.section-head')
    <ul class="tg-grid">
        @foreach ($settings['items'] ?? [] as $item)
            <li class="tg-item">
                <span class="tg-icon"><x-builder.icon :name="$item['icon'] ?? 'check'" /></span>
                <h3 class="tg-title">{{ $item['title'] ?? '' }}</h3>
                <p class="tg-text">{{ $item['text'] ?? '' }}</p>
            </li>
        @endforeach
    </ul>
</div>
