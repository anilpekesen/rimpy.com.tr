<div class="max-w-4xl space-y-4">

    <div class="card-pad">
        <h2 class="text-sm font-semibold text-ink-900">Ne işe yarar?</h2>
        <p class="mt-1 text-sm text-ink-600">
            Eski siteden taşınırken ya da bir sayfanın adresi değiştiğinde, eski adrese gelen ziyaretçiler
            ve arama motorları buradan yeni adrese yönlendirilir. Bu, Google'daki mevcut sıralamanızı korur.
        </p>
    </div>

    <form wire:submit="add" class="card-pad">
        <div class="grid gap-3 sm:grid-cols-[1fr_1fr_auto_auto] sm:items-start">
            <div>
                <label for="r-from" class="label">Eski adres</label>
                <input id="r-from" type="text" wire:model="from" class="field <?php $__errorArgs = ['from'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="/eski-sayfa.html">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['from'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label for="r-to" class="label">Yeni adres</label>
                <input id="r-to" type="text" wire:model="to" class="field <?php $__errorArgs = ['to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="/tr/hakkimizda">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['to'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label for="r-code" class="label">Tür</label>
                <select id="r-code" wire:model="statusCode" class="field w-32">
                    <option value="301">Kalıcı (301)</option>
                    <option value="302">Geçici (302)</option>
                </select>
            </div>
            <button type="submit" class="btn-primary mt-6.5">Ekle</button>
        </div>
        <p class="hint mt-2">Kalıcı yönlendirmede Google sıralamayı yeni adrese taşır. Emin değilseniz kalıcıyı seçin.</p>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($redirects->isEmpty()): ?>
        <div class="card flex flex-col items-center justify-center py-14 text-center">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'link','class' => 'size-9 text-ink-200']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'link','class' => 'size-9 text-ink-200']); ?>
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
            <p class="mt-3 text-sm font-medium text-ink-700">Henüz yönlendirme yok.</p>
        </div>
    <?php else: ?>
        <div class="card overflow-hidden">
            <table class="w-full text-sm">
                <caption class="sr-only">Adres yönlendirmeleri</caption>
                <thead>
                    <tr class="border-b border-ink-100 bg-ink-50/60 text-left text-xs font-medium text-ink-500">
                        <th scope="col" class="px-4 py-2.5">Eski adres</th>
                        <th scope="col" class="px-4 py-2.5">Yeni adres</th>
                        <th scope="col" class="px-4 py-2.5">Tür</th>
                        <th scope="col" class="px-4 py-2.5">Kullanım</th>
                        <th scope="col" class="px-4 py-2.5"><span class="sr-only">İşlem</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $redirects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $redirect): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="group">
                            <td class="px-4 py-2.5 font-mono text-xs text-ink-700"><?php echo e($redirect->from); ?></td>
                            <td class="px-4 py-2.5 font-mono text-xs text-ink-700"><?php echo e($redirect->to); ?></td>
                            <td class="px-4 py-2.5 text-xs text-ink-500"><?php echo e($redirect->status_code === 301 ? 'Kalıcı' : 'Geçici'); ?></td>
                            <td class="px-4 py-2.5 text-xs text-ink-500">
                                <?php echo e($redirect->hits); ?> kez
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($redirect->last_hit_at): ?>
                                    <span class="text-ink-400">· <?php echo e($redirect->last_hit_at->diffForHumans()); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <button wire:click="delete(<?php echo e($redirect->id); ?>)"
                                        class="btn-ghost btn-sm text-red-600 opacity-0 group-hover:opacity-100">
                                    <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'trash','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'trash','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?><span class="sr-only">Sil</span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php echo e($redirects->links()); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/admin/settings/redirects.blade.php ENDPATH**/ ?>