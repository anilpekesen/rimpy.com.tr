<?php
    $items = $settings['items'] ?? [];
    $cols = data_get($settings, 'columns.d.size', 3);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items): ?>
    <div class="cl tst-grid"
         style="grid-template-columns: repeat(<?php echo e(($settings['layout'] ?? 'grid') === 'grid' ? (int) $cols : 1); ?>, minmax(0, 1fr))">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $avatar = $context->media($item['avatar'] ?? null); ?>
            <figure class="tst">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($item['rating'])): ?>
                    <div class="tst-stars" aria-label="<?php echo e($item['rating']); ?> / 5">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < (int) $item['rating']; $i++): ?>
                            <?php if (isset($component)) { $__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.builder.icon','data' => ['name' => 'star','style' => 'fill:currentColor']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('builder.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'star','style' => 'fill:currentColor']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050)): ?>
<?php $attributes = $__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050; ?>
<?php unset($__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050)): ?>
<?php $component = $__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050; ?>
<?php unset($__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050); ?>
<?php endif; ?>
                        <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <blockquote class="tst-quote"><?php echo e($item['quote'] ?? ''); ?></blockquote>

                <figcaption class="tst-foot">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($avatar): ?>
                        <img class="tst-avatar" src="<?php echo e($avatar->url('thumb')); ?>" alt="" loading="lazy">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <span>
                        <span class="tst-name" style="display:block;font-weight:600"><?php echo e($item['name'] ?? ''); ?></span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($item['role'])): ?>
                            <span class="tst-role" style="display:block;font-size:.875em;opacity:.7"><?php echo e($item['role']); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                </figcaption>
            </figure>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/testimonial.blade.php ENDPATH**/ ?>