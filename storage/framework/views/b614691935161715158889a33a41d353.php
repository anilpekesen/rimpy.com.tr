<?php
    $value = (int) ($settings['value'] ?? 0);
    $display = ($settings['separator'] ?? true) ? number_format($value, 0, ',', '.') : (string) $value;
?>

<div class="counter">
    <span class="counter-value"
          data-counter="<?php echo e($value); ?>"
          data-counter-duration="<?php echo e((int) (data_get($settings, 'duration.size', 2) * 1000)); ?>"
          data-counter-separator="<?php echo e(($settings['separator'] ?? true) ? '1' : '0'); ?>">
        <?php echo e($settings['prefix'] ?? ''); ?><span data-counter-number><?php echo e($display); ?></span><?php echo e($settings['suffix'] ?? ''); ?>

    </span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($settings['title'])): ?>
        <span class="counter-title"><?php echo e($settings['title']); ?></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/counter.blade.php ENDPATH**/ ?>