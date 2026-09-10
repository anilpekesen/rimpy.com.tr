<?php $items = $settings['items'] ?? []; ?>

<div class="acc" <?php if($settings['single'] ?? true): ?> data-accordion-single <?php endif; ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <details class="acc-item" <?php if($item['open'] ?? false): ?> open <?php endif; ?>
                 <?php if($settings['single'] ?? true): ?> name="acc-<?php echo e($element->id); ?>" <?php endif; ?>>
            <summary class="acc-head">
                <span class="acc-title"><?php echo e($item['title'] ?? ''); ?></span>
                <span class="acc-icon"><?php if (isset($component)) { $__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $component; } ?>
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
<?php endif; ?></span>
            </summary>
            <div class="acc-body tx"><?php echo \App\Support\Html::sanitize($item['body'] ?? ''); ?></div>
        </details>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($settings['schema'] ?? true) && $items && ! $context->editing && $context->claimOnce('faq')): ?>
    <?php
        /**
         * Arama motorlarının soruları doğrudan sonuçlarda gösterebilmesi için.
         *
         * Yapı burada kuruluyor, şablonda değil: Schema.org anahtarları "@"
         * ile başladığı için Blade onları kendi yönergesi sanıyor ve
         * şablon içinde yazıldıklarında derleme hatası veriyor.
         */
        $faqSchema = \App\Support\Schema::faq($items);
    ?>

    <script type="application/ld+json"><?php echo \App\Support\Schema::encode($faqSchema); ?></script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/accordion.blade.php ENDPATH**/ ?>