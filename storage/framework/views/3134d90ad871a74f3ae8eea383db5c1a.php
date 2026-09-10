<?php $link = $settings['link'] ?? []; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($settings['eyebrow'])): ?>
    <span class="hd-eyebrow"><?php echo e($settings['eyebrow']); ?></span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($link['url'])): ?>
    <a href="<?php echo e($link['url']); ?>" target="<?php echo e($link['target']); ?>" <?php if($link['rel']): ?> rel="<?php echo e($link['rel']); ?>" <?php endif; ?> class="no-underline">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<<?php echo e($tag = in_array($settings['tag'] ?? 'h2', ['h1','h2','h3','h4','h5','h6','div'], true) ? $settings['tag'] : 'h2'); ?> class="hd">
    <?php echo e($settings['text'] ?? ''); ?>

</<?php echo e($tag); ?>>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($link['url'])): ?>
    </a>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/heading.blade.php ENDPATH**/ ?>