<?php $items = $settings['items'] ?? []; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items): ?>
    <div class="tabs tabs--<?php echo e($settings['orientation'] ?? 'horizontal'); ?>" data-tabs>
        <div class="tabs-list" role="tablist">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button class="tab-btn" role="tab" type="button"
                        id="tab-<?php echo e($element->id); ?>-<?php echo e($index); ?>"
                        aria-controls="panel-<?php echo e($element->id); ?>-<?php echo e($index); ?>"
                        aria-selected="<?php echo e($index === 0 ? 'true' : 'false'); ?>"
                        tabindex="<?php echo e($index === 0 ? '0' : '-1'); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($item['icon'])): ?> <?php if (isset($component)) { $__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.builder.icon','data' => ['name' => $item['icon']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('builder.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item['icon'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050)): ?>
<?php $attributes = $__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050; ?>
<?php unset($__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050)): ?>
<?php $component = $__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050; ?>
<?php unset($__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050); ?>
<?php endif; ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php echo e($item['title'] ?? ''); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="tab-panel tx" role="tabpanel"
                 id="panel-<?php echo e($element->id); ?>-<?php echo e($index); ?>"
                 aria-labelledby="tab-<?php echo e($element->id); ?>-<?php echo e($index); ?>"
                 <?php if($index !== 0): ?> hidden <?php endif; ?>>
                <?php echo \App\Support\Html::sanitize($item['body'] ?? ''); ?>

            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/tabs.blade.php ENDPATH**/ ?>