@php
    $context = new \App\Builder\RenderContext($locale, subject: $item);
    $tree = \App\Builder\Tree::fromArray($translation->builder_data);
    $cover = $item->featuredImage;
@endphp

<x-site-layout
    :locale="$locale"
    :subject="$item"
    :seo-title="$translation->seo_title ?: $translation->title.' — '.\App\Models\Setting::get('general.site_name')"
    :seo-description="$translation->seo_description ?: $translation->excerpt"
    :noindex="$translation->noindex"
    :og-image-id="$item->featured_image_id">

    @push('styles')
        <style>{!! $translation->compiled_css !!}</style>
    @endpush

    @if (! $tree->isEmpty())
        {{-- Serbest tasarım yapılmışsa onu göster --}}
        {!! app(\App\Builder\Renderer::class)->render($tree, $context) !!}
    @else
        <article style="max-width:var(--container);margin-inline:auto;padding:3rem max(16px,var(--gutter)) 5rem">
            <nav aria-label="Yol izi" style="margin-bottom:1.5rem">
                <ol class="crumbs" style="font-size:.875rem;color:var(--c-muted)">
                    @foreach (\App\Support\Breadcrumbs::for($item, $locale) as $index => $crumb)
                        <li class="crumb">
                            @if ($crumb['url'])
                                <a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a>
                            @else
                                <span aria-current="page">{{ $crumb['label'] }}</span>
                            @endif
                        </li>
                        @unless ($loop->last) <li aria-hidden="true">›</li> @endunless
                    @endforeach
                </ol>
            </nav>

            <h1 style="margin:0 0 .75rem;font-size:clamp(1.85rem,4vw,2.75rem)">{{ $translation->title }}</h1>

            @if ($item->published_at)
                <time datetime="{{ $item->published_at->toDateString() }}" style="color:var(--c-muted);font-size:.9375rem">
                    {{ $item->published_at->translatedFormat('d F Y') }}
                </time>
            @endif

            @if ($translation->excerpt)
                <p style="margin:1.25rem 0 0;font-size:1.125rem;color:var(--c-muted)">{{ $translation->excerpt }}</p>
            @endif

            @if ($cover)
                <img src="{{ $cover->url('large') }}" alt="{{ $cover->altText() }}"
                     @if ($srcset = $cover->srcset()) srcset="{{ $srcset }}" sizes="(max-width:1200px) 100vw, 1200px" @endif
                     style="margin:2rem 0;border-radius:var(--radius);width:100%" width="{{ $cover->width }}" height="{{ $cover->height }}">
            @endif

            @if ($translation->body)
                <div class="tx" style="margin-top:2rem">{!! \App\Support\Html::sanitize($translation->body) !!}</div>
            @endif

            {{-- Kullanıcının tanımladığı ek alanlar --}}
            @if ($type->fields && $translation->custom)
                <dl style="margin-top:2.5rem;display:grid;grid-template-columns:auto 1fr;gap:.5rem 1.5rem;font-size:.9375rem">
                    @foreach ($type->fields as $field)
                        @php $value = $translation->custom[$field['key']] ?? null; @endphp
                        @continue (blank($value))
                        <dt style="color:var(--c-muted)">{{ $field['label'][$locale] ?? $field['key'] }}</dt>
                        <dd style="margin:0;font-weight:500">{{ $value }}</dd>
                    @endforeach
                </dl>
            @endif

            @if ($item->gallery)
                @php $gallery = \App\Models\Media::whereIn('id', $item->gallery)->get(); @endphp
                <div class="gallery" style="margin-top:2.5rem;grid-template-columns:repeat(3,minmax(0,1fr))">
                    @foreach ($gallery as $image)
                        <figure class="gallery-item" style="aspect-ratio:1/1;border-radius:var(--radius)">
                            <a href="{{ $image->url('large') }}" data-lightbox="icerik">
                                <img src="{{ $image->url('medium') }}" alt="{{ $image->altText() }}" loading="lazy">
                            </a>
                        </figure>
                    @endforeach
                </div>
            @endif
        </article>
    @endif

    @php
        $crumbs = \App\Support\Schema::breadcrumbs(\App\Support\Breadcrumbs::for($item, $locale));
    @endphp

    @if ($json = \App\Support\Schema::encode($crumbs))
        <script type="application/ld+json">{!! $json !!}</script>
    @endif
</x-site-layout>
