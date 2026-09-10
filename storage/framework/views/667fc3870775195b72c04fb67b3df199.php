<?php
/**
 * Tekrarlayan listenin bir satırındaki tek alan.
 *
 * Değer değişince yalnızca o alanın koordinatı (liste anahtarı, satır sırası,
 * alan adı) sunucuya gider; listenin tamamı istemciye gömülmez.
 */
$fieldType = $field['type'] ?? 'text';
$fieldId = "rep-{$repeaterKey}-{$rowIndex}-{$fieldKey}";

// Alanın değerini sunucuya yazan ortak çağrı.
$write = "\$wire.setRepeaterField('".e($repeaterKey)."', {$rowIndex}, '".e($fieldKey)."', ";
?>

<div class="space-y-1" wire:key="<?php echo e($repeaterKey); ?>-<?php echo e($rowIndex); ?>-<?php echo e($fieldKey); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fieldType !== 'toggle'): ?>
        <label for="<?php echo e($fieldId); ?>" class="block text-[11px] font-medium text-ink-600"><?php echo e($field['label']); ?></label>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($fieldType):
        case ('textarea'): ?>
            <textarea id="<?php echo e($fieldId); ?>" rows="<?php echo e($field['rows'] ?? 2); ?>" class="field px-2 py-1.5 text-xs"
                      x-on:input.debounce.500ms="<?php echo $write; ?>$event.target.value)"><?php echo e(is_scalar($fieldValue) ? $fieldValue : ''); ?></textarea>
            <?php break; ?>

        <?php case ('rich'): ?>
            <textarea id="<?php echo e($fieldId); ?>" rows="4" class="field px-2 py-1.5 font-mono text-[11px]"
                      x-on:input.debounce.500ms="<?php echo $write; ?>$event.target.value)"><?php echo e(is_scalar($fieldValue) ? $fieldValue : ''); ?></textarea>
            <p class="text-[10px] text-ink-400">Basit HTML kullanabilirsiniz: &lt;p&gt;, &lt;strong&gt;, &lt;a&gt;</p>
            <?php break; ?>

        <?php case ('select'): ?>
            <select id="<?php echo e($fieldId); ?>" class="field px-2 py-1.5 text-xs"
                    x-on:change="<?php echo $write; ?>$event.target.value)">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $field['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optionValue => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($optionValue); ?>" <?php if($fieldValue == $optionValue): echo 'selected'; endif; ?>><?php echo e($optionLabel); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php break; ?>

        <?php case ('toggle'): ?>
            <label class="flex items-center gap-2 text-xs text-ink-700">
                <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600" <?php if($fieldValue): echo 'checked'; endif; ?>
                       x-on:change="<?php echo $write; ?>$event.target.checked)">
                <?php echo e($field['label']); ?>

            </label>
            <?php break; ?>

        <?php case ('media'): ?>
            <?php $rowMedia = is_numeric($fieldValue) ? \App\Models\Media::find($fieldValue) : null; ?>
            <div class="flex items-center gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rowMedia): ?>
                    <img src="<?php echo e($rowMedia->url('thumb')); ?>" alt="" class="size-10 shrink-0 rounded border border-ink-200 object-cover">
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button type="button" class="btn-secondary btn-sm flex-1"
                        @click="$dispatch('open-media-picker', {
                            repeater: <?php echo \Illuminate\Support\Js::from($repeaterKey)->toHtml() ?>, row: <?php echo e($rowIndex); ?>,
                            field: <?php echo \Illuminate\Support\Js::from($fieldKey)->toHtml() ?>, accept: 'image'
                        })">
                    <?php echo e($rowMedia ? 'Değiştir' : 'Görsel seç'); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rowMedia): ?>
                    <button type="button" class="btn-ghost btn-sm text-red-600"
                            x-on:click="<?php echo $write; ?>null)" title="Kaldır">×</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php break; ?>

        <?php case ('link'): ?>
            <input id="<?php echo e($fieldId); ?>" type="text" class="field px-2 py-1.5 text-xs" placeholder="https://…"
                   value="<?php echo e(is_array($fieldValue) ? ($fieldValue['url'] ?? '') : $fieldValue); ?>"
                   x-on:input.debounce.500ms="<?php echo $write; ?>{ type: 'url', url: $event.target.value, target: '_self' })">
            <?php break; ?>

        <?php case ('icon'): ?>
            <select id="<?php echo e($fieldId); ?>" class="field px-2 py-1.5 text-xs"
                    x-on:change="<?php echo $write; ?>$event.target.value)">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                    '' => 'Yok', 'check' => 'Onay', 'check-circle' => 'Onay (daire)', 'star' => 'Yıldız',
                    'arrow-right' => 'Sağ ok', 'phone' => 'Telefon', 'mail' => 'Zarf',
                    'map-pin' => 'Konum', 'shield' => 'Kalkan', 'award' => 'Ödül',
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iconValue => $iconLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($iconValue); ?>" <?php if($fieldValue === $iconValue): echo 'selected'; endif; ?>><?php echo e($iconLabel); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
            <?php break; ?>

        <?php default: ?>
            <input id="<?php echo e($fieldId); ?>" type="text" class="field px-2 py-1.5 text-xs"
                   value="<?php echo e(is_scalar($fieldValue) ? $fieldValue : ''); ?>"
                   x-on:input.debounce.500ms="<?php echo $write; ?>$event.target.value)">
    <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/repeater-field.blade.php ENDPATH**/ ?>