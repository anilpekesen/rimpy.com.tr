<?php $poster = $settings['poster'] ?? null; ?>

<div class="video" <?php if($poster): ?> style="background-image:url('<?php echo e($poster->url('large')); ?>');background-size:cover;background-position:center" <?php endif; ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($settings['source'] ?? 'youtube') === 'file' && ($file = $settings['file'] ?? null)): ?>
        <video
            <?php if($settings['controls'] ?? true): ?> controls <?php endif; ?>
            <?php if($settings['autoplay'] ?? false): ?> autoplay <?php endif; ?>
            <?php if($settings['muted'] ?? true): ?> muted playsinline <?php endif; ?>
            <?php if($settings['loop'] ?? false): ?> loop <?php endif; ?>
            <?php if($poster): ?> poster="<?php echo e($poster->url('large')); ?>" <?php endif; ?>
            preload="metadata">
            <source src="<?php echo e($file->url()); ?>" type="<?php echo e($file->mime); ?>">
        </video>
    <?php elseif($embed): ?>
        
        <div x-data="{ playing: <?php echo e(($settings['autoplay'] ?? false) ? 'true' : 'false'); ?> }" class="absolute inset-0">
            <template x-if="playing">
                <iframe src="<?php echo e($embed); ?>" title="<?php echo e($settings['title'] ?? 'Video'); ?>"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen loading="lazy"></iframe>
            </template>
            <button type="button" x-show="!playing" @click="playing = true"
                    class="absolute inset-0 flex items-center justify-center"
                    style="background: rgba(0,0,0,.25)" aria-label="Videoyu oynat">
                <span style="display:flex;align-items:center;justify-content:center;width:72px;height:72px;border-radius:999px;background:rgba(255,255,255,.92);color:#111">
                    <?php if (isset($component)) { $__componentOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0f0d3a3348c99d941b2cbc2da1b81050 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.builder.icon','data' => ['name' => 'play','style' => 'width:28px;height:28px']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('builder.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'play','style' => 'width:28px;height:28px']); ?>
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
                </span>
            </button>
        </div>
    <?php elseif($context->editing): ?>
        <div class="absolute inset-0 flex items-center justify-center text-sm text-white/70">
            Video adresi girilmedi ya da tanınmadı.
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/video.blade.php ENDPATH**/ ?>