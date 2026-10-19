@php
    use App\Models\Term;
    use App\Models\Translation;

    $prefix = \App\Support\Urls::prefix($locale);
    $base = $prefix.'/'.$type->prefix($locale);
    $title = $term ? $term->t('name', $locale) : $type->pluralLabel($locale);

    // Kategori çipleri: ana listede ana kategoriler, bir kategorideyken onun
    // alt kategorileri (alt kategorideyken kardeşleri).
    $taxonomy = $type->taxonomies->firstWhere('key', 'kategori');
    $parentId = $term ? ($term->parent_id ?: $term->id) : null;
    $chips = $taxonomy ? Term::withTranslations()->with('taxonomy')
        ->where('taxonomy_id', $taxonomy->id)->where('is_active', true)
        ->where('parent_id', $parentId)->orderBy('sort')->get() : collect();
    $parentTerm = $parentId ? Term::withTranslations()->with('taxonomy')->find($parentId) : null;
    $termUrl = fn ($t) => $prefix.$t->path($locale);
@endphp

<x-site-layout
    :locale="$locale"
    :seo-title="$title.' — '.\App\Models\Setting::get('general.site_name')"
    :seo-description="$term?->t('seo_description', $locale) ?: $term?->t('description', $locale)">

    <div class="pa">
        <header class="pa-head">
            <nav class="pd-crumbs" aria-label="Breadcrumb">
                <a href="{{ $base }}">{{ $type->pluralLabel($locale) }}</a>
                @if ($parentTerm && $term && $parentTerm->id !== $term->id)
                    <span aria-hidden="true">/</span><a href="{{ $termUrl($parentTerm) }}">{{ $parentTerm->t('name', $locale) }}</a>
                @endif
            </nav>
            <h1 class="pa-title">{{ $title }}</h1>
            @if ($description = $term?->t('description', $locale))
                <p class="pa-lead">{{ $description }}</p>
            @endif

            <form class="pa-search" role="search" method="GET">
                <x-icon name="search" />
                <input type="search" name="q" value="{{ $search }}" aria-label="{{ Translation::get('search', 'Ara', $locale) }}"
                       placeholder="{{ $locale === 'tr' ? 'Model kodu ya da ürün adı (örn. RSC-5013)' : 'Model code or product name' }}">
            </form>
        </header>

        @if ($chips->isNotEmpty())
            <nav class="pa-chips" aria-label="{{ Translation::get('category', 'Kategori', $locale) }}">
                <a href="{{ $parentTerm ? $termUrl($parentTerm) : $base }}" @class(['pa-chip', 'is-active' => ! $term || $term->id === $parentId])>
                    {{ Translation::get('all', 'Tümü', $locale) }}
                </a>
                @foreach ($chips as $chip)
                    <a href="{{ $termUrl($chip) }}" @class(['pa-chip', 'is-active' => $term?->id === $chip->id])>{{ $chip->t('name', $locale) }}</a>
                @endforeach
            </nav>
        @endif

        @if ($items->isEmpty())
            <p class="pa-empty">{{ Translation::get('no_results', 'Sonuç bulunamadı.', $locale) }}</p>
        @else
            <div class="pc-grid">
                @foreach ($items as $product)
                    <x-product.card :item="$product" :locale="$locale" />
                @endforeach
            </div>

            <div class="pa-pages">{{ $items->links() }}</div>
        @endif
    </div>
</x-site-layout>
