<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo e($title ?? 'Giriş'); ?> — <?php echo e(\App\Models\Setting::get('general.site_name', config('app.name'))); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="flex min-h-full items-center justify-center bg-ink-100 px-4 py-12">
    <div class="w-full max-w-sm">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-ink-900 text-lg font-semibold text-white">
                <?php echo e(mb_substr(\App\Models\Setting::get('general.site_name', config('app.name')), 0, 1)); ?>

            </div>
            <h1 class="text-lg font-semibold text-ink-900">
                <?php echo e(\App\Models\Setting::get('general.site_name', config('app.name'))); ?>

            </h1>
            <p class="mt-1 text-sm text-ink-500">Yönetim paneli</p>
        </div>

        <?php echo e($slot); ?>


        <p class="mt-8 text-center text-xs text-ink-400">
            Bu sayfaya erişim kayıt altına alınır.
        </p>
    </div>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/layouts/guest.blade.php ENDPATH**/ ?>