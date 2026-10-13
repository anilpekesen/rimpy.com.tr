{{--
    Sayfa bulunamadı.

    Sitenin kendi başlığı, menüsü ve alt bilgisiyle çizilir; ziyaretçiye
    çıkış yolları sunar: model koduyla ürün arama, ana kategoriler, araçlar
    ve teklif sepeti. Katalog sitesinde en sık 404 sebebi eski ya da yanlış
    yazılmış bir ürün adresidir, bu yüzden arama en üstte.

    Durum kodu 404 kalır, ana sayfaya yönlendirme yapılmaz (yumuşak 404
    olurdu) ve sayfa arama motorlarına kapalıdır.
--}}
@php
    use App\Support\Locales;

    // 404 hiçbir rotayla eşleşmediği için dil burada adresten çözülür.
    $codes = Locales::all()->pluck('code')->all();
    $segment = request()->segment(1);
    $locale = in_array($segment, $codes, true)
        ? $segment
        : (in_array(app()->getLocale(), $codes, true) ? app()->getLocale() : Locales::default());
    app()->setLocale($locale);

    $tr = $locale === 'tr';
    $prefix = \App\Support\Urls::prefix($locale);
    $type = \App\Models\ContentType::with('taxonomies')->where('key', 'urunler')->first();
    $archive = $type ? $prefix.'/'.$type->prefix($locale) : $prefix.'/';
    $taxonomy = $type?->taxonomies->firstWhere('key', 'kategori');
    $categories = $taxonomy ? \App\Models\Term::withTranslations()->with('taxonomy')
        ->where('taxonomy_id', $taxonomy->id)->whereNull('parent_id')->where('is_active', true)->orderBy('sort')->get() : collect();
    $torquePage = \App\Models\Page::whereHas('translations', fn ($q) => $q->where('slug', $tr ? 'tork-hesaplayici' : 'torque-calculator'))->first();

    // Adresteki son parça çoğu zaman aranan model kodudur: /urunler/rsc-5013-… → "rsc-5013"
    $guess = \Illuminate\Support\Str::of(request()->segment(count(request()->segments())) ?? '')
        ->match('/^[a-z]{2,4}-?[a-z0-9]{2,6}/i')->upper()->toString();
@endphp

<x-site-layout :locale="$locale" :noindex="true" :seo-title="($tr ? 'Sayfa bulunamadı' : 'Page not found').' — Rimpy'">
    <section class="e404 tone-dark">
        <div class="e404-grid" aria-hidden="true"></div>
        <div class="e404-inner">
            <div class="e404-copy">
                <span class="sh-eyebrow">{{ $tr ? 'Hata 404 · Sayfa bulunamadı' : 'Error 404 · Page not found' }}</span>
                <h1 class="e404-title">{!! $tr ? 'Bu cıvata<br><em>yerinde değil.</em>' : 'This bolt<br><em>came loose.</em>' !!}</h1>
                <p class="e404-lead">{{ $tr
                    ? 'Aradığınız sayfa taşınmış, adı değişmiş ya da hiç var olmamış olabilir. Model koduyla arayın ya da aşağıdan devam edin.'
                    : 'The page you are looking for may have moved, been renamed or never existed. Search by model code or continue below.' }}</p>

                <form class="e404-search" role="search" method="GET" action="{{ $archive }}">
                    <x-icon name="search" />
                    <input type="search" name="q" value="{{ $guess }}" autofocus
                           aria-label="{{ $tr ? 'Ürün ara' : 'Search products' }}"
                           placeholder="{{ $tr ? 'Model kodu ya da ürün adı (örn. RSC-5013)' : 'Model code or product name (e.g. RSC-5013)' }}">
                    <button type="submit" class="xb xb--y">{{ $tr ? 'Ara' : 'Search' }}</button>
                </form>
            </div>
            <p class="e404-code" aria-hidden="true">4<span>0</span>4</p>
        </div>
    </section>

    <section class="e404-ways">
        <div class="e404-ways-inner">
            @foreach ($categories as $category)
                <a class="e404-way" href="{{ $prefix.'/'.$type->prefix($locale).'/'.$category->taxonomy->prefix($locale).'/'.$category->t('slug', $locale) }}">
                    <span>{{ $tr ? 'Kategori' : 'Category' }}</span>
                    <b>{{ $category->t('name', $locale) }}</b>
                    <x-icon name="arrow-right" />
                </a>
            @endforeach
            @if ($torquePage)
                <a class="e404-way" href="{{ $prefix.rtrim($torquePage->path($locale), '/') }}">
                    <span>{{ $tr ? 'Araç' : 'Tool' }}</span>
                    <b>{{ $tr ? 'Tork hesaplayıcı' : 'Torque calculator' }}</b>
                    <x-icon name="arrow-right" />
                </a>
            @endif
            <button type="button" class="e404-way e404-way--y" data-quote-open>
                <span>{{ $tr ? 'Bulamadınız mı?' : 'Can\'t find it?' }}</span>
                <b>{{ $tr ? 'Teklif isteyin, biz bulalım' : 'Ask us for a quote' }}</b>
                <x-icon name="arrow-right" />
            </button>
        </div>
        <p class="e404-home"><a href="{{ url($prefix ?: '/') }}">← {{ $tr ? 'Ana sayfaya dön' : 'Back to home' }}</a></p>
    </section>
</x-site-layout>
