@php
    $cols = data_get($settings, 'columns.d.size', 3);
    $layout = $settings['layout'] ?? 'grid';
    $prefix = $context->prefix();
@endphp

@if ($items->isEmpty())
    <p class="cl-empty">{{ $settings['empty_text'] ?? 'Henüz içerik eklenmedi.' }}</p>
@else
    <div class="cl cl--{{ $layout }} @if ($hover = $settings['hover'] ?? null) cl--{{ $hover }} @endif"
         style="grid-template-columns: repeat({{ $layout === 'list' ? 1 : (int) $cols }}, minmax(0, 1fr))">

        @foreach ($items as $item)
            @php
                $url = $prefix.$item->path($context->locale);
                $image = $item->featuredImage;
                $category = $item->terms->first();
            @endphp

            <article class="cl-card">
                @if (($settings['show_image'] ?? true) && $image)
                    <a class="cl-media" href="{{ $url }}" tabindex="-1" aria-hidden="true"
                       @if ($ratio = $settings['image_ratio'] ?? null) style="aspect-ratio:{{ $ratio }}" @endif>
                        <img src="{{ $image->url('medium') }}" alt="{{ $image->altText() }}" loading="lazy" decoding="async">
                    </a>
                @endif

                <div class="cl-body">
                    @if (($settings['show_category'] ?? false) && $category)
                        <span class="cl-meta">{{ $category->t('name', $context->locale) }}</span>
                    @endif

                    @if ($settings['show_title'] ?? true)
                        <h3 class="cl-title">
                            <a href="{{ $url }}">{{ $item->t('title', $context->locale) }}</a>
                        </h3>
                    @endif

                    @if ($settings['show_date'] ?? false)
                        <time class="cl-meta" datetime="{{ $item->published_at?->toDateString() }}">
                            {{ $item->published_at?->translatedFormat('d F Y') }}
                        </time>
                    @endif

                    @if ($settings['show_excerpt'] ?? true)
                        @php
                            $excerpt = $item->t('excerpt', $context->locale)
                                ?: \App\Support\Html::excerpt($item->t('body', $context->locale), (int) ($settings['excerpt_length'] ?? 20));
                        @endphp
                        @if ($excerpt)
                            <p class="cl-excerpt">{{ \Illuminate\Support\Str::words(strip_tags($excerpt), (int) ($settings['excerpt_length'] ?? 20), '…') }}</p>
                        @endif
                    @endif

                    @if ($settings['show_button'] ?? false)
                        <a class="cl-more" href="{{ $url }}">
                            {{ $settings['button_text'] ?? 'Devamını oku' }}
                            <x-builder.icon name="arrow-right"/>
                        </a>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    @if (($settings['pagination'] ?? false) && method_exists($items, 'links'))
        <div class="cl-pagination">{{ $items->links() }}</div>
    @endif
@endif
