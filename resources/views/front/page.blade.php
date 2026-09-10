@php
    $context = new \App\Builder\RenderContext($locale, subject: $page);
    $tree = \App\Builder\Tree::fromArray($translation->builder_data);
@endphp

<x-site-layout
    :locale="$locale"
    :subject="$page"
    :seo-title="$translation->seo_title ?: $translation->title.' — '.\App\Models\Setting::get('general.site_name')"
    :seo-description="$translation->seo_description ?: $translation->excerpt"
    :noindex="$translation->noindex"
    :og-image-id="$translation->og_image_id"
    :canonical="$translation->canonical_url ?: url()->current()">

    @push('styles')
        <style>{!! $translation->compiled_css !!}</style>
    @endpush

    @if ($tree->isEmpty())
        <div style="max-width:var(--container);margin-inline:auto;padding:6rem max(16px,var(--gutter))">
            <h1>{{ $translation->title }}</h1>
            @if ($translation->excerpt)
                <p>{{ $translation->excerpt }}</p>
            @endif
        </div>
    @else
        {!! app(\App\Builder\Renderer::class)->render($tree, $context) !!}
    @endif

    @if ($page->is_home || $page->parent_id)
        <script type="application/ld+json">@json(
            \App\Support\Breadcrumbs::schema(\App\Support\Breadcrumbs::for($page, $locale)),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        )</script>
    @endif
</x-site-layout>
