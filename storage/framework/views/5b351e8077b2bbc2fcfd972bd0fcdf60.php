<?php
    $media = $settings['image'] ?? null;
    $link = $settings['link'] ?? [];
    $hover = $settings['hover'] ?? '';
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($media): ?>
    <figure class="img-frame <?php if($hover): ?> img-frame--<?php echo e($hover); ?> <?php endif; ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($link['url'])): ?>
            <a href="<?php echo e($link['url']); ?>" target="<?php echo e($link['target']); ?>" <?php if($link['rel']): ?> rel="<?php echo e($link['rel']); ?>" <?php endif; ?>>
        <?php elseif(! empty($settings['lightbox'])): ?>
            <a href="<?php echo e($media->url()); ?>" data-lightbox>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <img src="<?php echo e($media->url($settings['size'] ?? 'large')); ?>"
             <?php if($srcset = $media->srcset()): ?> srcset="<?php echo e($srcset); ?>" sizes="(max-width: 768px) 100vw, 50vw" <?php endif; ?>
             alt="<?php echo e($media->altText()); ?>"
             <?php if($media->width): ?> width="<?php echo e($media->width); ?>" height="<?php echo e($media->height); ?>" <?php endif; ?>
             loading="<?php echo e(($settings['lazy'] ?? true) ? 'lazy' : 'eager'); ?>"
             decoding="async">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($link['url']) || ! empty($settings['lightbox'])): ?>
            </a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($settings['caption'])): ?>
            <figcaption class="img-caption"><?php echo e($settings['caption']); ?></figcaption>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </figure>
<?php elseif($context->editing): ?>
    <div class="img-frame flex aspect-[4/3] items-center justify-center bg-ink-100 text-sm text-ink-400">
        Görsel seçilmedi
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/image.blade.php ENDPATH**/ ?>