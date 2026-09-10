<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'locale',
    'subject' => null,
    'seoTitle' => null,
    'seoDescription' => null,
    'noindex' => false,
    'ogImageId' => null,
    'canonical' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'locale',
    'subject' => null,
    'seoTitle' => null,
    'seoDescription' => null,
    'noindex' => false,
    'ogImageId' => null,
    'canonical' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $theme = app(\App\Support\Theme::class);
    $settings = \App\Models\Setting::all();
    $siteName = data_get($settings, 'general.site_name', config('app.name'));
    $language = \App\Support\Locales::all()->firstWhere('code', $locale);

    $header = \App\Models\Template::where('type', 'header')->where('is_default', true)->first();
    $footer = \App\Models\Template::where('type', 'footer')->where('is_default', true)->first();

    $context = new \App\Builder\RenderContext($locale, subject: $subject ?? null);
    $renderer = app(\App\Builder\Renderer::class);
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($language->direction ?? 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo e($seoTitle ?? $siteName); ?></title>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($description = $seoDescription ?? data_get($settings, "seo.meta_description.{$locale}")): ?>
        <meta name="description" content="<?php echo e(\Illuminate\Support\Str::limit(strip_tags($description), 300)); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($noindex ?? false) || data_get($settings, 'seo.noindex_site')): ?>
        <meta name="robots" content="noindex, nofollow">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <link rel="canonical" href="<?php echo e($canonical ?? url()->current()); ?>">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = \App\Support\Urls::alternates(request()->path()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $href): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <link rel="alternate" hreflang="<?php echo e($code); ?>" href="<?php echo e($href); ?>">
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo e($siteName); ?>">
    <meta property="og:title" content="<?php echo e($seoTitle ?? $siteName); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ogImage = \App\Models\Media::find($ogImageId ?? data_get($settings, 'seo.og_image_id'))): ?>
        <meta property="og:image" content="<?php echo e($ogImage->url('large')); ?>">
        <meta name="twitter:card" content="summary_large_image">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($favicon = \App\Models\Media::find(data_get($settings, 'general.favicon_id'))): ?>
        <link rel="icon" href="<?php echo e($favicon->url('thumb')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fontUrl = $theme->fontUrl()): ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="<?php echo e($fontUrl); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/builder.css', 'resources/js/site.js']); ?>

    
    <style>
        <?php echo $theme->cssVariables(); ?>

        body{margin:0;background:var(--c-background);color:var(--c-text);font-family:var(--font-body);font-size:var(--text-base);line-height:var(--leading);-webkit-font-smoothing:antialiased}
        h1,h2,h3,h4,h5,h6{font-family:var(--font-heading);color:var(--c-heading);line-height:1.2}
        a{color:inherit}
        img,svg,video{max-width:100%;height:auto}
        .skip-link{position:absolute;left:-9999px}
        .skip-link:focus{left:1rem;top:1rem;z-index:99;background:#fff;padding:.75rem 1rem;border-radius:var(--radius)}
        :focus-visible{outline:2px solid var(--c-primary);outline-offset:2px}
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ga = data_get($settings, 'seo.google_analytics')): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e($ga); ?>"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','<?php echo e($ga); ?>')</script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($verification = data_get($settings, 'seo.google_verification')): ?>
        <meta name="google-site-verification" content="<?php echo e($verification); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</head>
<body>
    <a href="#main" class="skip-link">İçeriğe geç</a>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($header): ?>
        <header><?php echo $renderer->template($header, $context); ?></header>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <main id="main"><?php echo e($slot); ?></main>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footer): ?>
        <footer><?php echo $renderer->template($footer, $context); ?></footer>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/components/site-layout.blade.php ENDPATH**/ ?>