<?php
    $q = is_array($value) ? $value : [];
    $types = \App\Models\ContentType::where('is_active', true)->orderBy('sort')->get();
    $selectedType = $types->firstWhere('key', $q['type'] ?? null);
    $terms = $selectedType
        ? \App\Models\Term::whereHas('taxonomy', fn ($t) => $t->where('content_type_id', $selectedType->id))
            ->with('translations', 'taxonomy')->get()
        : collect();
?>

<div class="space-y-2 rounded-lg border border-ink-200 p-2.5" wire:key="<?php echo e($selected['id']); ?>-<?php echo e($key); ?>-query">
    <label class="block">
        <span class="mb-1 block text-[11px] text-ink-500">İçerik türü</span>
        <select class="field px-1.5 py-1 text-xs"
                x-on:change="$wire.setSetting('<?php echo e($key); ?>', Object.assign(<?php echo \Illuminate\Support\Js::from($q)->toHtml() ?>, { type: $event.target.value, terms: [] }), false)">
            <option value="">— Seçin —</option>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($t->key); ?>" <?php if(($q['type'] ?? null) === $t->key): echo 'selected'; endif; ?>><?php echo e($t->pluralLabel('tr')); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </select>
    </label>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($terms->isNotEmpty()): ?>
        <div>
            <span class="mb-1 block text-[11px] text-ink-500">Yalnızca şu kategorilerden</span>
            <div class="max-h-32 space-y-1 overflow-y-auto rounded-md border border-ink-200 p-1.5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex items-center gap-1.5 text-xs text-ink-700">
                        <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600"
                               <?php if(in_array($term->id, $q['terms'] ?? [])): echo 'checked'; endif; ?>
                               x-on:change="
                                    const cur = <?php echo \Illuminate\Support\Js::from(array_values($q['terms'] ?? []))->toHtml() ?>;
                                    const next = $event.target.checked ? [...cur, <?php echo e($term->id); ?>] : cur.filter(i => i !== <?php echo e($term->id); ?>);
                                    $wire.setSetting('<?php echo e($key); ?>', Object.assign(<?php echo \Illuminate\Support\Js::from($q)->toHtml() ?>, { terms: next }), false);
                               ">
                        <?php echo e($term->t('name')); ?>

                        <span class="text-ink-400">(<?php echo e($term->taxonomy->label('tr')); ?>)</span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <p class="mt-1 text-[10px] text-ink-400">Hiçbiri seçilmezse tüm kategoriler gösterilir.</p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="grid grid-cols-2 gap-2">
        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Kaç tane</span>
            <input type="number" min="1" max="48" class="field px-1.5 py-1 text-xs" value="<?php echo e($q['limit'] ?? 6); ?>"
                   x-on:input.debounce.400ms="$wire.setSetting('<?php echo e($key); ?>', Object.assign(<?php echo \Illuminate\Support\Js::from($q)->toHtml() ?>, { limit: Number($event.target.value) }), false)">
        </label>
        <label class="block">
            <span class="mb-1 block text-[11px] text-ink-500">Sıralama</span>
            <select class="field px-1.5 py-1 text-xs"
                    x-on:change="$wire.setSetting('<?php echo e($key); ?>', Object.assign(<?php echo \Illuminate\Support\Js::from($q)->toHtml() ?>, { order: $event.target.value }), false)">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['recent' => 'En yeniler önce', 'oldest' => 'En eskiler önce', 'manual' => 'Elle sıralama', 'popular' => 'En çok görüntülenen', 'random' => 'Rastgele']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ov => $ol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($ov); ?>" <?php if(($q['order'] ?? 'recent') === $ov): echo 'selected'; endif; ?>><?php echo e($ol); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </select>
        </label>
    </div>

    <label class="flex items-center gap-2 text-xs text-ink-700">
        <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600" <?php if($q['featured_only'] ?? false): echo 'checked'; endif; ?>
               x-on:change="$wire.setSetting('<?php echo e($key); ?>', Object.assign(<?php echo \Illuminate\Support\Js::from($q)->toHtml() ?>, { featured_only: $event.target.checked }), false)">
        Yalnızca öne çıkarılanlar
    </label>

    <label class="flex items-center gap-2 text-xs text-ink-700">
        <input type="checkbox" class="size-3.5 rounded border-ink-300 text-brand-600" <?php if($q['exclude_current'] ?? false): echo 'checked'; endif; ?>
               x-on:change="$wire.setSetting('<?php echo e($key); ?>', Object.assign(<?php echo \Illuminate\Support\Js::from($q)->toHtml() ?>, { exclude_current: $event.target.checked }), false)">
        Bulunulan içeriği listeden çıkar
    </label>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/builder/partials/control-query.blade.php ENDPATH**/ ?>