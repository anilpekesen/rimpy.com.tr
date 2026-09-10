<?php
    $bg = is_array($value) ? $value : [];
    $bgType = $bg['type'] ?? 'color';
    $theme = app(\App\Support\Theme::class);
?>

<div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>-bg"
     x-data="{
        v: <?php echo \Illuminate\Support\Js::from($bg + ['type' => 'color', 'color' => '', 'url' => '', 'position' => 'center center', 'size' => 'cover', 'repeat' => 'no-repeat', 'attachment' => '', 'from' => '#365ef8', 'to' => '#0f172a', 'angle' => 180])->toHtml() ?>,
        push() { $wire.setSetting('<?php echo e($key); ?>', this.v, false) }
     }">

    <div class="flex rounded-lg border border-ink-200 p-0.5">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['color' => 'Renk', 'gradient' => 'Geçiş', 'image' => 'Görsel']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option => $optionLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button" @click="v.type = '<?php echo e($option); ?>'; push()"
                    class="flex-1 rounded-md px-2 py-1 text-xs font-medium transition"
                    :class="v.type === '<?php echo e($option); ?>' ? 'bg-ink-900 text-white' : 'text-ink-500 hover:bg-ink-100'">
                <?php echo e($optionLabel); ?>

            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div x-show="v.type === 'color'" class="space-y-1.5">
        <div class="flex flex-wrap gap-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $theme->get('colors', []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token => $hex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" @click="v.color = <?php echo \Illuminate\Support\Js::from('@'.$token)->toHtml() ?>; push()"
                        class="size-6 rounded-md border-2 border-ink-200 transition hover:border-ink-400"
                        :class="v.color === <?php echo \Illuminate\Support\Js::from('@'.$token)->toHtml() ?> && '!border-brand-600'"
                        style="background: <?php echo e($hex); ?>" title="<?php echo e($token); ?>">
                    <span class="sr-only"><?php echo e($token); ?></span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="flex items-center gap-2">
            <input type="color" class="size-8 shrink-0 cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                   x-model="v.color" @input.debounce.300ms="push()" aria-label="Zemin rengi">
            <input type="text" class="field flex-1 font-mono text-xs" x-model="v.color"
                   @input.debounce.400ms="push()" placeholder="#ffffff">
            <button type="button" class="btn-ghost btn-sm" @click="v.color = ''; push()" title="Temizle">×</button>
        </div>
    </div>

    
    <div x-show="v.type === 'gradient'" class="space-y-2">
        <div class="grid grid-cols-2 gap-2">
            <label class="block">
                <span class="mb-1 block text-[11px] text-ink-500">Başlangıç</span>
                <input type="color" class="h-8 w-full cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                       x-model="v.from" @input.debounce.300ms="push()">
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] text-ink-500">Bitiş</span>
                <input type="color" class="h-8 w-full cursor-pointer rounded border border-ink-300 bg-white p-0.5"
                       x-model="v.to" @input.debounce.300ms="push()">
            </label>
        </div>
        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Yön (derece)</span>
            <input type="range" min="0" max="360" step="15" class="w-full accent-brand-600"
                   x-model.number="v.angle" @change="push()">
        </label>
    </div>

    
    <div x-show="v.type === 'image'" class="space-y-2">
        <template x-if="v.url">
            <div class="relative overflow-hidden rounded-lg border border-ink-200">
                <img :src="v.url" alt="" class="h-24 w-full object-cover">
                <button type="button" @click="v.url = ''; push()"
                        class="absolute top-1 right-1 rounded-md bg-white/90 px-1.5 py-0.5 text-xs font-medium text-red-600 shadow-sm">
                    Kaldır
                </button>
            </div>
        </template>

        <button type="button" @click="$dispatch('open-media-picker', { target: '<?php echo e($key); ?>.url', mode: 'url' })"
                @media-applied.stop="v.url = $event.detail.url; push()"
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
<?php endif; ?> <span x-text="v.url ? 'Görseli değiştir' : 'Görsel seç'"></span>
        </button>

        <div class="grid grid-cols-2 gap-2">
            <label class="block">
                <span class="mb-1 block text-[11px] text-ink-500">Konum</span>
                <select class="field px-1.5 py-1 text-xs" x-model="v.position" @change="push()">
                    <option value="center center">Ortada</option>
                    <option value="center top">Üstte</option>
                    <option value="center bottom">Altta</option>
                    <option value="left center">Solda</option>
                    <option value="right center">Sağda</option>
                </select>
            </label>
            <label class="block">
                <span class="mb-1 block text-[11px] text-ink-500">Sığdırma</span>
                <select class="field px-1.5 py-1 text-xs" x-model="v.size" @change="push()">
                    <option value="cover">Alanı doldur</option>
                    <option value="contain">Tamamı görünsün</option>
                    <option value="auto">Gerçek boyut</option>
                </select>
            </label>
        </div>

        <label class="flex items-center gap-2 text-xs text-ink-700">
            <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600"
                   :checked="v.attachment === 'fixed'"
                   @change="v.attachment = $event.target.checked ? 'fixed' : ''; push()">
            Sayfa kayarken görsel sabit kalsın
        </label>

        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Görsel üstü renk (okunabilirlik için)</span>
            <input type="text" class="field font-mono text-xs" x-model="v.color"
                   @input.debounce.400ms="push()" placeholder="rgba(0,0,0,.4)">
        </label>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/control-background.blade.php ENDPATH**/ ?>