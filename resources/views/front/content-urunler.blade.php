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
    $price = $item->custom('baslangic_fiyati', $locale);

    // Bilgi kutusundaki maddeler: panelde yazılanlar, yoksa teknik
    // özelliklerin ilk satırları.
    $points = array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) $item->custom('maddeler', $locale)))))
        ?: array_map(fn ($spec) => $spec['label'].': '.$spec['value'], array_slice($specs, 0, 5));

    // Önce alt kategori; ekmek kırıntısı üst kategoriden ona iner.
    $category = $item->terms->sortByDesc(fn ($term) => (int) filled($term->parent_id))->first();
    $parent = $category?->parent_id ? $item->terms->firstWhere('id', $category->parent_id) : null;
    $termUrl = fn ($term) => $prefix.$term->path($locale);

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
            {{-- 2 + 3: büyük görsel ve altında ek fotoğraflar; yükseklik bilgi kutusuyla aynı. --}}
            <div class="pd-gallery" data-pd-gallery>
                <figure class="pd-stage">
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

            {{-- 1: başlık, madde madde özellikler ve teklif. --}}
            <div class="pd-info">
                @if ($code)<span class="pd-code">{{ $code }}</span>@endif
                <h1 class="pd-title">{{ $title }}</h1>

                @if ($translation->excerpt)
                    <p class="pd-lead">{{ $translation->excerpt }}</p>
                @endif

                @if ($points)
                    <ul class="pd-points">
                        @foreach ($points as $point)
                            <li>{{ $point }}</li>
                        @endforeach
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
            </div>
        </div>

        {{-- 4: sekmeler. Betik yoksa tüm bölümler alt alta görünür. --}}
        @php
            $tabs = array_filter([
                'ozellikler' => $specs ? $tr('specifications', 'Teknik özellikler') : null,
                'detay' => $translation->body ? $tr('description', 'Ürün detayı') : null,
                'belgeler' => $documents ? $tr('documents', 'Belgeler') : null,
                'muadil' => $equivalents ? $tr('equivalents', 'Muadil modeller') : null,
            ]);
        @endphp

        @if ($tabs)
            <div class="pd-tabs" data-pd-tabs>
                <div class="pd-tablist" role="tablist">
                    @foreach ($tabs as $key => $label)
                        <button type="button" role="tab" id="pd-tab-{{ $key }}" aria-controls="pd-panel-{{ $key }}"
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}" @class(['pd-tab', 'is-active' => $loop->first])>{{ $label }}</button>
                    @endforeach
                </div>

                @isset ($tabs['ozellikler'])
                    <section class="pd-panel" role="tabpanel" id="pd-panel-ozellikler" aria-labelledby="pd-tab-ozellikler">
                        <h2 class="pd-h">{{ $tabs['ozellikler'] }}</h2>
                        <table class="pd-table">
                            <tbody>
                                @if ($code)<tr><th scope="row">{{ $type->field('model_kodu')['label'][$locale] ?? 'Model' }}</th><td>{{ $code }}</td></tr>@endif
                                @foreach ($specs as $spec)
                                    <tr><th scope="row">{{ $spec['label'] }}</th><td>{{ $spec['value'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </section>
                @endisset

                @isset ($tabs['detay'])
                    <section class="pd-panel pd-body tx" role="tabpanel" id="pd-panel-detay" aria-labelledby="pd-tab-detay">
                        <h2 class="pd-h">{{ $tabs['detay'] }}</h2>
                        {!! \App\Support\Html::sanitize($translation->body) !!}
                    </section>
                @endisset

                @isset ($tabs['belgeler'])
                    <section class="pd-panel" role="tabpanel" id="pd-panel-belgeler" aria-labelledby="pd-tab-belgeler">
                        <h2 class="pd-h">{{ $tabs['belgeler'] }}</h2>
                        <ul class="pd-docs">
                            @foreach ($documents as $document)
                                <li><a href="{{ $document['url'] }}" target="_blank" rel="noopener"><x-icon name="download" />{{ $document['label'] }}</a></li>
                            @endforeach
                        </ul>
                    </section>
                @endisset

                @isset ($tabs['muadil'])
                    <section class="pd-panel" role="tabpanel" id="pd-panel-muadil" aria-labelledby="pd-tab-muadil">
                        <h2 class="pd-h">{{ $tabs['muadil'] }}</h2>
                        <p class="pd-note">{{ $locale === 'tr'
                            ? 'Bu ürün aşağıdaki modellerin muadili olarak kullanılabilir. Marka adları yalnızca karşılaştırma amaçlıdır.'
                            : 'This product can be used as an equivalent to the models below. Brand names are for comparison only.' }}</p>
                        <ul class="pd-equivalents">
                            @foreach ($equivalents as $equivalent)
                                <li><span>{{ $equivalent }}</span><b>→ {{ $code }}</b></li>
                            @endforeach
                        </ul>
                    </section>
                @endisset
            </div>
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
