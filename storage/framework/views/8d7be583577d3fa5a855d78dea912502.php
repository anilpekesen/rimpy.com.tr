<?php $sh = is_array($value) ? $value : []; ?>

<div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>-shadow"
     x-data="{
        v: <?php echo \Illuminate\Support\Js::from($sh + ['enabled' => false, 'x' => 0, 'y' => 8, 'blur' => 24, 'spread' => 0, 'color' => 'rgba(0,0,0,0.12)'])->toHtml() ?>,
        push() { $wire.setSetting('<?php echo e($key); ?>', this.v.enabled ? this.v : null, false) }
     }">

    <label class="flex items-center gap-2 text-sm text-ink-700">
        <input type="checkbox" class="size-4 rounded border-ink-300 text-brand-600" x-model="v.enabled" @change="push()">
        Gölge kullan
    </label>

    <div x-show="v.enabled" class="space-y-2">
        <div class="grid grid-cols-2 gap-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['x' => 'Yatay', 'y' => 'Dikey', 'blur' => 'Yumuşaklık', 'spread' => 'Yayılım']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $fieldLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="block">
                    <span class="mb-1 block text-[11px] text-ink-500"><?php echo e($fieldLabel); ?></span>
                    <input type="number" class="field px-1.5 py-1 text-xs"
                           x-model.number="v.<?php echo e($field); ?>" @input.debounce.400ms="push()">
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Renk</span>
            <input type="text" class="field font-mono text-xs" x-model="v.color" @input.debounce.400ms="push()">
        </label>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/control-shadow.blade.php ENDPATH**/ ?>