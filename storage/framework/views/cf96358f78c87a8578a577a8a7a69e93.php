<?php
    $ids = is_array($value) ? $value : [];
    $items = $ids ? \App\Models\Media::whereIn('id', $ids)->get()->keyBy('id') : collect();
?>

<div class="space-y-2" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>-gallery">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($ids): ?>
        <ul class="grid grid-cols-4 gap-1.5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $ids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $media = $items->get($id); ?>
                <?php if(! $media) continue; ?>
                <li class="group relative aspect-square overflow-hidden rounded-md border border-ink-200">
                    <img src="<?php echo e($media->url('thumb')); ?>" alt="<?php echo e($media->altText()); ?>" class="size-full object-cover">
                    <button type="button"
                            x-on:click="$wire.setSetting('<?php echo e($key); ?>', <?php echo \Illuminate\Support\Js::from(array_values(array_diff($ids, [$id])))->toHtml() ?>, false)"
                            class="absolute inset-0 flex items-center justify-center bg-ink-950/60 text-xs font-medium text-white opacity-0 transition group-hover:opacity-100"
                            title="Kaldır">
                        Kaldır
                    </button>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>
        <p class="hint"><?php echo e(count($ids)); ?> görsel seçili. Sıralamayı değiştirmek için kaldırıp yeniden ekleyin.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <button type="button" x-on:click="$dispatch('open-media-picker', { target: '<?php echo e($key); ?>', multiple: true, existing: <?php echo \Illuminate\Support\Js::from($ids)->toHtml() ?> })"
            class="btn-secondary btn-sm w-full">
        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'image','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'image','class' => 'size-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <?php echo e($ids ? 'Görsel ekle' : 'Görselleri seç'); ?>

    </button>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/control-gallery.blade.php ENDPATH**/ ?>