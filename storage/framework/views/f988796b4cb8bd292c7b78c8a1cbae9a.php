<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($settings['embed'])): ?>
    <div class="map <?php if($settings['grayscale'] ?? false): ?> map--gray <?php endif; ?>">
        <iframe src="<?php echo e($settings['embed']); ?>" title="Konum haritası"
                loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
    </div>
<?php elseif($context->editing): ?>
    <div class="map flex items-center justify-center bg-ink-100 text-sm text-ink-400" style="min-height:200px">
        Adres girilmedi.
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/map.blade.php ENDPATH**/ ?>