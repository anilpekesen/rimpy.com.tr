<?php
    $logos = $settings['logos'] ?? collect();
    $cols = data_get($settings, 'columns.d.size', 5);
    $marquee = ($settings['layout'] ?? 'grid') === 'marquee';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logos->isNotEmpty()): ?>
    <div class="logo-strip <?php if($settings['grayscale'] ?? true): ?> logo-strip--gray <?php endif; ?> <?php if($marquee): ?> logo-strip--marquee <?php endif; ?>"
         <?php if($marquee): ?>
             style="--marquee-speed: <?php echo e((int) data_get($settings, 'speed.size', 35)); ?>s"
         <?php else: ?>
             style="grid-template-columns: repeat(<?php echo e((int) $cols); ?>, minmax(0, 1fr))"
         <?php endif; ?>>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $logos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="logo-item">
                <img src="<?php echo e($logo->url('small')); ?>" alt="<?php echo e($logo->altText()); ?>" loading="lazy" decoding="async">
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($marquee): ?>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $logos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="logo-item" aria-hidden="true">
                    <img src="<?php echo e($logo->url('small')); ?>" alt="" loading="lazy">
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/logo_strip.blade.php ENDPATH**/ ?>