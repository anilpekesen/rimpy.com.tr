@php
    $prefix = \App\Support\Urls::prefix($context->locale);
    $url = fn ($term) => $prefix.$term->path($context->locale);
    $limit = (int) ($settings['children'] ?? 5);
@endphp

<div class="ct">
    @include('builder.widgets.partials.section-head')

    <div class="ct-grid">
        @forelse ($terms as $term)
            @php $image = $context->media($term->image_id ?: ($fallbacks[$term->id] ?? null)); @endphp
            <a class="ct-tile" href="{{ $url($term) }}">
                @if ($image)
                    <img class="ct-img" src="{{ $image->url('large') }}" alt="" loading="lazy">
                @endif
                <span class="ct-shade" aria-hidden="true"></span>
                <span class="ct-top"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span>{{ $term->items_count }} {{ $context->locale === 'tr' ? 'ürün' : 'products' }}</span></span>
                <span class="ct-body">
                    <span class="ct-name">{{ $term->t('name', $context->locale) }}</span>
                    @if ($term->children->isNotEmpty())
                        <span class="ct-chips">
                            @foreach ($term->children->take($limit) as $child)
                                <span>{{ $child->t('name', $context->locale) }}</span>
                            @endforeach
                        </span>
                    @endif
                </span>
                <span class="ct-go" aria-hidden="true"><x-icon name="arrow-right" /></span>
            </a>
        @empty
            @if ($context->editing)<p>Ürün kategorisi bulunamadı.</p>@endif
        @endforelse
    </div>
</div>
