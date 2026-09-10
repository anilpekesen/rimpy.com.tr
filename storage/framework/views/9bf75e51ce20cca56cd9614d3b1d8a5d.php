<?php
    $languages = \App\Support\Locales::all();
    $display = $settings['display'] ?? 'code';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($languages->count() > 1): ?>
    <div class="lang lang--<?php echo e($settings['style'] ?? 'inline'); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                // Ziyaretçi bulunduğu sayfanın diğer dildeki karşılığına gider;
                // karşılığı yoksa o dilin ana sayfasına düşer.
                $target = \App\Support\Urls::translate(request()->path(), $context->locale, $language->code);
            ?>
            <a class="lang-link <?php if($language->code === $context->locale): ?> is-active <?php endif; ?>"
               href="<?php echo e($target); ?>" hreflang="<?php echo e($language->code); ?>"
               <?php if($language->code === $context->locale): ?> aria-current="true" <?php endif; ?>>
                <?php echo e($display === 'name' ? $language->native_name : strtoupper($language->code)); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/language_switcher.blade.php ENDPATH**/ ?>