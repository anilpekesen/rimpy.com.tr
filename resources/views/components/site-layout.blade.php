@props([
    'locale',
    'subject' => null,
    'seoTitle' => null,
    'seoDescription' => null,
    'noindex' => false,
    'ogImageId' => null,
    'canonical' => null,
    // Kurucunun önizleme çerçevesi: istatistik betiği basılmaz.
    'preview' => false,
    // false: üst başlık ve alt bilgi çizilmez (şablon düzenlenirken).
    'chrome' => true,
])

@php
    $theme = app(\App\Support\Theme::class);
    $settings = \App\Models\Setting::all();
    $siteName = data_get($settings, 'general.site_name', config('app.name'));
    $language = \App\Support\Locales::all()->firstWhere('code', $locale);

    $header = \App\Models\Template::where('type', 'header')->where('is_default', true)->first();
    $footer = \App\Models\Template::where('type', 'footer')->where('is_default', true)->first();
    $isHome = $subject instanceof \App\Models\Page && $subject->is_home;

    $context = new \App\Builder\RenderContext($locale, subject: $subject ?? null);
    $renderer = app(\App\Builder\Renderer::class);
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $language->direction ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $seoTitle ?? $siteName }}</title>

    @if ($description = $seoDescription ?? data_get($settings, "seo.meta_description.{$locale}"))
        <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 300) }}">
    @endif

    @if (($noindex ?? false) || data_get($settings, 'seo.noindex_site'))
        <meta name="robots" content="noindex, nofollow">
    @endif

    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

    {{-- Dil karşılıkları --}}
    @foreach (\App\Support\Urls::alternates(request()->path()) as $code => $href)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $href }}">
    @endforeach

    {{-- Paylaşım kartı --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $seoTitle ?? $siteName }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if ($ogImage = \App\Models\Media::find($ogImageId ?? data_get($settings, 'seo.og_image_id')))
        <meta property="og:image" content="{{ $ogImage->url('large') }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    @if ($favicon = \App\Models\Media::find(data_get($settings, 'general.favicon_id')))
        <link rel="icon" href="{{ $favicon->url('thumb') }}">
    @endif

    @if ($fontUrl = $theme->fontUrl())
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="{{ $fontUrl }}">
    @endif

    @vite(['resources/css/builder.css', 'resources/js/site.js'])

    {{-- Tema jetonları ve bu sayfaya özel derlenmiş stiller --}}
    <style>
        {!! $theme->cssVariables() !!}
        {{-- Üst başlık ve alt bilgi de kurucuda tasarlandığı için kendi
             derlenmiş stillerini taşır; sayfanınkiyle birlikte basılmazsa
             her sayfada biçimsiz görünürler. --}}
        {!! $header?->compiled_css !!}
        {!! $footer?->compiled_css !!}
        body{margin:0;background:var(--c-background);color:var(--c-text);font-family:var(--font-body);font-size:var(--text-base);line-height:var(--leading);-webkit-font-smoothing:antialiased}
        h1,h2,h3,h4,h5,h6{font-family:var(--font-heading);color:var(--c-heading);line-height:1.2}
        a{color:inherit}
        img,svg,video{max-width:100%;height:auto}
        .skip-link{position:absolute;left:-9999px}
        .skip-link:focus{left:1rem;top:1rem;z-index:99;background:#fff;padding:.75rem 1rem;border-radius:var(--radius)}
        :focus-visible{outline:2px solid var(--c-primary);outline-offset:2px}
    </style>
    @stack('styles')

    @if (! $preview && ($ga = data_get($settings, 'seo.google_analytics')))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $ga }}')</script>
    @endif

    @if ($verification = data_get($settings, 'seo.google_verification'))
        <meta name="google-site-verification" content="{{ $verification }}">
    @endif
</head>
<body class="{{ $isHome ? 'page-home' : '' }}">
    <a href="#main" class="skip-link">İçeriğe geç</a>

    @if ($chrome && $header)
        <header>{!! $renderer->template($header, $context) !!}</header>
    @endif

    <main id="main">{{ $slot }}</main>

    @if ($chrome && $footer)
        <footer>{!! $renderer->template($footer, $context) !!}</footer>
    @endif

    @stack('scripts')
</body>
</html>
