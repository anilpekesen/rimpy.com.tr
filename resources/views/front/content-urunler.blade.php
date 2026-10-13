@php
    use App\Models\Translation;
    use App\Support\ProductSpecs;

    $tr = fn (string $key, string $fallback) => Translation::get($key, $fallback, $locale);
    $prefix = \App\Support\Urls::prefix($locale);
    $title = $translation->title;
    $code = $item->custom('model_kodu', $locale);
    $specs = ProductSpecs::rows($item, $locale);
    $documents = ProductSpecs::documents($item, $locale);
    $equivalents = ProductSpecs::equivalents($item, $locale);
    $stock = $item->custom('stok_durumu', $locale);
    $leadTime = $item->custom('teslim_suresi', $locale);
    $price = $item->custom('baslangic_fiyati', $locale);
    $badge = $item->custom('rozet', $locale);

    // Önce alt kategori; ekmek kırıntısı üst kategoriden ona iner.
    $category = $item->terms->sortByDesc(fn ($term) => (int) filled($term->parent_id))->first();
    $parent = $category?->parent_id ? $item->terms->firstWhere('id', $category->parent_id) : null;
    $termUrl = fn ($term) => $prefix.'/'.$type->prefix($locale).'/'.$term->taxonomy->prefix($locale).'/'.$term->t('slug', $locale);

    $images = collect([$item->featuredImage])
        ->merge($item->gallery ? \App\Models\Media::whereIn('id', $item->gallery)->get() : [])
        ->filter()->values();

    // WhatsApp mesajı ürünü tanıtarak başlar; mühendis neye bakıldığını hemen bilir.
    $whatsapp = preg_replace('/\D+/', '', (string) \App\Models\Setting::get('contact.whatsapp'));
    $whatsappText = rawurlencode(trim(($code ? $code.' — ' : '').$title)."\n".url()->current()."\n\n");

    $related = $category ? \App\Models\ContentItem::withTranslations()
        ->with('featuredImage', 'type')
        ->where('content_type_id', $type->id)
        ->whereKeyNot($item->getKey())
        ->whereHas('terms', fn ($q) => $q->where('terms.id', $category->id))
        ->live()->orderBy('sort')->limit(4)->get() : collect();
@endphp

<x-site-layout
    :locale="$locale"
    :subject="$item"
    :seo-title="$translation->seo_title ?: $title.' — '.\App\Models\Setting::get('general.site_name')"
    :seo-description="$translation->seo_description ?: $translation->excerpt"
    :noindex="$translation->noindex"
    :og-image-id="$item->featured_image_id">

    @push('styles')
        <script type="application/ld+json">{!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $title,
            'sku' => $code,
            'mpn' => $code,
            'description' => $translation->excerpt,
            'image' => $item->featuredImage?->url('large'),
            'brand' => ['@type' => 'Brand', 'name' => \App\Models\Setting::get('general.site_name')],
            'category' => $category?->t('name', $locale),
        ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    @endpush

    <div class="pd">
        <nav class="pd-crumbs" aria-label="Breadcrumb">
            <a href="{{ $prefix.'/'.$type->prefix($locale) }}">{{ $type->pluralLabel($locale) }}</a>
            @if ($parent)<span aria-hidden="true">/</span><a href="{{ $termUrl($parent) }}">{{ $parent->t('name', $locale) }}</a>@endif
            @if ($category)<span aria-hidden="true">/</span><a href="{{ $termUrl($category) }}">{{ $category->t('name', $locale) }}</a>@endif
        </nav>

        <div class="pd-top">
            <div class="pd-gallery" data-pd-gallery>
                <figure class="pd-stage">
                    @if ($badge)<span class="pc-badge">{{ $badge }}</span>@endif
                    @if ($images->isNotEmpty())
                        <img src="{{ $images[0]->url('large') }}" alt="{{ $images[0]->altText() ?: $title }}"
                             @if ($srcset = $images[0]->srcset()) srcset="{{ $srcset }}" sizes="(max-width: 960px) 100vw, 640px" @endif
                             width="{{ $images[0]->width }}" height="{{ $images[0]->height }}" data-pd-main>
                    @else
                        <x-product.placeholder :code="$code" />
                    @endif
                </figure>
                @if ($images->count() > 1)
                    <div class="pd-thumbs">
                        @foreach ($images as $image)
                            <button type="button" class="pd-thumb @if ($loop->first) is-active @endif" data-pd-thumb="{{ $image->url('large') }}"
                                    aria-label="{{ $title }} {{ $loop->iteration }}">
                                <img src="{{ $image->url('thumb') }}" alt="" loading="lazy">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="pd-info">
                @if ($code)<span class="pd-code">{{ $code }}</span>@endif
                <h1 class="pd-title">{{ $title }}</h1>

                @if ($translation->excerpt)
                    <p class="pd-lead">{{ $translation->excerpt }}</p>
                @endif

                @if ($stock || $leadTime)
                    <ul class="pd-status">
                        @if ($stock)
                            <li class="{{ \Illuminate\Support\Str::of($stock)->lower()->contains('stok') ? 'is-ok' : '' }}">
                                <span>{{ $tr('stock', 'Stok') }}</span><b>{{ $stock }}</b>
                            </li>
                        @endif
                        @if ($leadTime)<li><span>{{ $tr('lead_time', 'Teslim') }}</span><b>{{ $leadTime }}</b></li>@endif
                    </ul>
                @endif

                @if ($price)
                    <p class="pd-price"><span>{{ $tr('starting_price', 'Başlangıç fiyatı') }}</span> {{ $price }}</p>
                @endif

                <div class="pd-actions">
                    <x-quote.add-button :item="$item" :locale="$locale" class="pd-add" />
                    @if ($whatsapp)
                        <a class="pd-ask" href="https://wa.me/{{ $whatsapp }}?text={{ $whatsappText }}" target="_blank" rel="noopener">
                            <x-icon name="chat" /> {{ $tr('ask_engineer', 'Mühendise sor') }}
                        </a>
                    @endif
                </div>

                @if ($specs)
                    <dl class="pd-keyspecs">
                        @foreach (array_slice($specs, 0, 4) as $spec)
                            <div><dt>{{ $spec['label'] }}</dt><dd>{{ $spec['value'] }}</dd></div>
                        @endforeach
                    </dl>
                @endif
            </div>
        </div>

        <div @class(['pd-details', 'pd-details--single' => ! $documents && ! $equivalents])>
            @if ($specs)
                <section class="pd-block">
                    <h2 class="pd-h">{{ $tr('specifications', 'Teknik özellikler') }}</h2>
                    <table class="pd-table">
                        <tbody>
                            @if ($code)<tr><th scope="row">{{ $type->field('model_kodu')['label'][$locale] ?? 'Model' }}</th><td>{{ $code }}</td></tr>@endif
                            @foreach ($specs as $spec)
                                <tr><th scope="row">{{ $spec['label'] }}</th><td>{{ $spec['value'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </section>
            @endif

            <aside class="pd-side">
                @if ($documents)
                    <section class="pd-block">
                        <h2 class="pd-h">{{ $tr('documents', 'Belgeler') }}</h2>
                        <ul class="pd-docs">
                            @foreach ($documents as $document)
                                <li><a href="{{ $document['url'] }}" target="_blank" rel="noopener"><x-icon name="download" />{{ $document['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($equivalents)
                    <section class="pd-block">
                        <h2 class="pd-h">{{ $tr('equivalents', 'Muadil modeller') }}</h2>
                        <p class="pd-note">{{ $locale === 'tr'
                            ? 'Bu ürün aşağıdaki modellerin muadili olarak kullanılabilir. Marka adları yalnızca karşılaştırma amaçlıdır.'
                            : 'This product can be used as an equivalent to the models below. Brand names are for comparison only.' }}</p>
                        <ul class="pd-equivalents">
                            @foreach ($equivalents as $equivalent)
                                <li><span>{{ $equivalent }}</span><b>→ {{ $code }}</b></li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </aside>
        </div>

        @if ($translation->body)
            <section class="pd-block pd-body tx">{!! \App\Support\Html::sanitize($translation->body) !!}</section>
        @endif

        @if ($related->isNotEmpty())
            <section class="pd-related">
                <h2 class="pd-h">{{ Translation::get('related_products', 'Benzer ürünler', $locale) }}</h2>
                <div class="pc-grid">
                    @foreach ($related as $other)
                        <x-product.card :item="$other" :locale="$locale" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-site-layout>
