<?php
    $current = '/'.ltrim(request()->path(), '/');
    $prefix = $context->prefix();
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $menu): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($context->editing): ?>
        <p class="text-sm text-ink-400">Menü seçilmedi.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <nav class="nav-wrap" aria-label="<?php echo e($menu->name); ?>"
         data-menu data-mobile="<?php echo e($settings['mobile'] ?? 'drawer'); ?>"
         data-breakpoint="<?php echo e($settings['breakpoint'] ?? 'tablet'); ?>">

        <button class="nav-toggle" data-menu-toggle aria-expanded="false" aria-label="Menüyü aç">
            <span></span><span></span><span></span>
        </button>

        <ul class="nav <?php if(($settings['orientation'] ?? 'horizontal') === 'vertical'): ?> nav--vertical <?php endif; ?>
                   <?php if($indicator = $settings['indicator'] ?? null): ?> nav--<?php echo e($indicator); ?> <?php endif; ?>"
            data-menu-list>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menu->tree; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $href = $item->href($context->locale); ?>
                <?php if(! $href) continue; ?>

                <li class="nav-item-el <?php if($item->activeChildren->isNotEmpty()): ?> nav-item-el--has-sub <?php endif; ?>">
                    <a class="nav-link <?php if(rtrim($href, '/') === rtrim($current, '/')): ?> is-active <?php endif; ?>"
                       href="<?php echo e($href); ?>" target="<?php echo e($item->target); ?>"
                       <?php if($item->target === '_blank'): ?> rel="noopener" <?php endif; ?>
                       <?php if(rtrim($href, '/') === rtrim($current, '/')): ?> aria-current="page" <?php endif; ?>>
                        <?php echo e($item->title($context->locale)); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->activeChildren->isNotEmpty()): ?>
                            <?php if (isset($component)) { $__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.builder.icon','data' => ['name' => 'chevron-down']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('builder.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down']); ?>
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
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->activeChildren->isNotEmpty()): ?>
                        <ul class="nav-sub">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $item->activeChildren; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $childHref = $child->href($context->locale); ?>
                                <?php if(! $childHref) continue; ?>
                                <li>
                                    <a class="nav-link" href="<?php echo e($childHref); ?>" target="<?php echo e($child->target); ?>"
                                       <?php if($child->target === '_blank'): ?> rel="noopener" <?php endif; ?>>
                                        <?php echo e($child->title($context->locale)); ?>

                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
    </nav>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/menu.blade.php ENDPATH**/ ?>