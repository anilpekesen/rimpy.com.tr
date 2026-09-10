@php
    $title = $term ? $term->t('name', $locale) : $type->pluralLabel($locale);
    $prefix = \App\Support\Urls::prefix($locale);
@endphp

<x-site-layout
    :locale="$locale"
    :seo-title="$title.' — '.\App\Models\Setting::get('general.site_name')"
    :seo-description="$term?->t('seo_description', $locale)">

    <div style="max-width:var(--container);margin-inline:auto;padding:3rem max(16px,var(--gutter)) 5rem">
        <h1 style="margin:0 0 2rem;font-size:clamp(1.85rem,4vw,2.75rem)">{{ $title }}</h1>

        @if ($items->isEmpty())
            <p style="color:var(--c-muted)">{{ \App\Models\Translation::get('no_results', 'Sonuç bulunamadı.', $locale) }}</p>
        @else
            <div class="cl cl--grid cl--lift" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
                @foreach ($items as $item)
                    @php $image = $item->featuredImage; @endphp
                    <article class="cl-card" style="border:1px solid var(--c-border);border-radius:var(--radius);background:var(--c-background)">
                        @if ($image)
                            <a class="cl-media" href="{{ $prefix.$item->path($locale) }}" tabindex="-1" aria-hidden="true"
                               style="aspect-ratio:4/3">
                                <img src="{{ $image->url('medium') }}" alt="{{ $image->altText() }}" loading="lazy" decoding="async">
                            </a>
                        @endif
                        <div class="cl-body">
                            <h2 class="cl-title" style="font-size:1.125rem">
                                <a href="{{ $prefix.$item->path($locale) }}" style="text-decoration:none">
                                    {{ $item->t('title', $locale) }}
                                </a>
                            </h2>
                            @if ($excerpt = $item->t('excerpt', $locale))
                                <p class="cl-excerpt" style="color:var(--c-muted);font-size:.9375rem">
                                    {{ \Illuminate\Support\Str::words(strip_tags($excerpt), 22, '…') }}
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>

            <div style="margin-top:2.5rem">{{ $items->links() }}</div>
        @endif
    </div>
</x-site-layout>
