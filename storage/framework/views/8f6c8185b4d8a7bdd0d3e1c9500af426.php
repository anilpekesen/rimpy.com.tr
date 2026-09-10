<?php
    $images = $settings['images'] ?? collect();
    $columns = $settings['columns'] ?? [];
    $cols = is_array($columns) ? ($columns['d']['size'] ?? 3) : 3;
    $layout = $settings['layout'] ?? 'grid';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($images->isNotEmpty()): ?>
    <div class="gallery gallery--<?php echo e($layout); ?> <?php if($settings['hover'] ?? null): ?> gallery--<?php echo e($settings['hover']); ?> <?php endif; ?>"
         style="grid-template-columns: repeat(<?php echo e((int) $cols); ?>, minmax(0, 1fr))">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <figure class="gallery-item" <?php if($ratio = $settings['ratio'] ?? null): ?> style="aspect-ratio:<?php echo e($ratio); ?>" <?php endif; ?>>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['lightbox'] ?? true): ?>
                    <a href="<?php echo e($image->url('large')); ?>" data-lightbox="galeri-<?php echo e($element->id); ?>">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <img src="<?php echo e($image->url('medium')); ?>" alt="<?php echo e($image->altText()); ?>" loading="lazy" decoding="async">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settings['lightbox'] ?? true): ?> </a> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($settings['captions'] ?? false) && $caption = $image->altText()): ?>
                    <figcaption class="img-caption"><?php echo e($caption); ?></figcaption>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </figure>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php elseif($context->editing): ?>
    <p class="text-sm text-ink-400">Henüz görsel seçilmedi.</p>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/gallery.blade.php ENDPATH**/ ?>