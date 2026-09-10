<?php
    $rows = is_array($value) ? array_values($value) : [];
    $titleField = $control['title_field'] ?? array_key_first($control['fields']);
?>

<div class="space-y-2" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>-rep" x-data="{ open: null }">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="overflow-hidden rounded-lg border border-ink-200" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>-row-<?php echo e($index); ?>">
            <div class="flex items-center gap-1 bg-ink-50 px-2 py-1.5">
                <button type="button" @click="open = open === <?php echo e($index); ?> ? null : <?php echo e($index); ?>"
                        class="flex min-w-0 flex-1 items-center gap-1.5 text-left"
                        :aria-expanded="open === <?php echo e($index); ?>">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'chevron-down','class' => 'size-3.5 shrink-0 text-ink-400 transition',':class' => 'open === '.e($index).' && \'rotate-180\'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','class' => 'size-3.5 shrink-0 text-ink-400 transition',':class' => 'open === '.e($index).' && \'rotate-180\'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                    <span class="truncate text-xs font-medium text-ink-700">
                        <?php echo e(\Illuminate\Support\Str::limit(strip_tags((string) ($row[$titleField] ?? '')), 28) ?: 'Satır '.($index + 1)); ?>

                    </span>
                </button>

                <button type="button" class="btn-ghost btn-sm shrink-0" title="Yukarı taşı" <?php if($index === 0): echo 'disabled'; endif; ?>
                        wire:click="moveRepeaterRow(<?php echo \Illuminate\Support\Js::from($key)->toHtml() ?>, <?php echo e($index); ?>, 'up')">↑</button>
                <button type="button" class="btn-ghost btn-sm shrink-0" title="Aşağı taşı" <?php if($index === count($rows) - 1): echo 'disabled'; endif; ?>
                        wire:click="moveRepeaterRow(<?php echo \Illuminate\Support\Js::from($key)->toHtml() ?>, <?php echo e($index); ?>, 'down')">↓</button>
                <button type="button" class="btn-ghost btn-sm shrink-0 text-red-600 hover:bg-red-50" title="Satırı sil"
                        wire:click="removeRepeaterRow(<?php echo \Illuminate\Support\Js::from($key)->toHtml() ?>, <?php echo e($index); ?>)"
                        wire:confirm="Bu satır silinecek. Devam edilsin mi?">
                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'size-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                </button>
            </div>

            <div x-show="open === <?php echo e($index); ?>" x-collapse class="space-y-2.5 border-t border-ink-100 p-2.5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $control['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fieldKey => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('livewire.builder.partials.repeater-field', [
                        'rowIndex' => $index,
                        'repeaterKey' => $key,
                        'fieldKey' => $fieldKey,
                        'field' => $field,
                        'fieldValue' => $row[$fieldKey] ?? ($field['default'] ?? null),
                    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <button type="button" class="btn-secondary btn-sm w-full" wire:click="addRepeaterRow(<?php echo \Illuminate\Support\Js::from($key)->toHtml() ?>)">
        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'plus','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'size-3.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> <?php echo e($control['add_label'] ?? 'Satır ekle'); ?>

    </button>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/control-repeater.blade.php ENDPATH**/ ?>