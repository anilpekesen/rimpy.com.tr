<form wire:submit="save" class="grid max-w-5xl gap-5 lg:grid-cols-[1fr_20rem]">

    <div class="min-w-0 space-y-4">
        <section class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-ink-100 px-4 py-2.5">
                <h2 class="text-sm font-semibold text-ink-900">Form alanları</h2>
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open" class="btn-secondary btn-sm">
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
<?php endif; ?> Alan ekle
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 z-20 mt-1 w-44 overflow-hidden rounded-lg border border-ink-200 bg-white py-1 shadow-lg">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fieldTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <button type="button" wire:click="addField('<?php echo e($key); ?>')" @click="open = false"
                                    class="block w-full px-3 py-1.5 text-left text-sm text-ink-700 hover:bg-ink-50">
                                <?php echo e($label); ?>

                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $fields): ?>
                <p class="px-4 py-12 text-center text-sm text-ink-400">Henüz alan yok. Sağ üstten ekleyin.</p>
            <?php else: ?>
                <ul class="divide-y divide-ink-100">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="space-y-3 p-4">
                            <div class="flex items-center gap-2">
                                <span class="badge-gray shrink-0"><?php echo e($fieldTypes[$field['type']] ?? $field['type']); ?></span>
                                <span class="ml-auto flex shrink-0 gap-0.5">
                                    <button type="button" wire:click="moveField(<?php echo e($index); ?>, 'up')" class="btn-ghost btn-sm" <?php if($index === 0): echo 'disabled'; endif; ?>>↑</button>
                                    <button type="button" wire:click="moveField(<?php echo e($index); ?>, 'down')" class="btn-ghost btn-sm" <?php if($index === count($fields) - 1): echo 'disabled'; endif; ?>>↓</button>
                                    <button type="button" wire:click="removeField(<?php echo e($index); ?>)" class="btn-ghost btn-sm text-red-600">
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
<?php endif; ?><span class="sr-only">Kaldır</span>
                                    </button>
                                </span>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div>
                                        <label for="fl-<?php echo e($index); ?>-<?php echo e($locale->code); ?>" class="label">
                                            Etiket
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locales->count() > 1): ?> <span class="font-normal text-ink-400">(<?php echo e($locale->native_name); ?>)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </label>
                                        <input id="fl-<?php echo e($index); ?>-<?php echo e($locale->code); ?>" type="text"
                                               wire:model="fields.<?php echo e($index); ?>.label.<?php echo e($locale->code); ?>"
                                               class="field <?php $__errorArgs = ['fields.'.$index.'.label.'.$locale->code];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fields.'.$index.'.label.'.$locale->code];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                <div>
                                    <label for="fk-<?php echo e($index); ?>" class="label">Anahtar</label>
                                    <input id="fk-<?php echo e($index); ?>" type="text" wire:model="fields.<?php echo e($index); ?>.key"
                                           class="field font-mono text-xs <?php $__errorArgs = ['fields.'.$index.'.key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['fields.'.$index.'.key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <p class="error"><?php echo e($message); ?></p>
                                    <?php else: ?>
                                        <p class="hint">Gelen mesajlarda bu adla görünür.</p>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div>
                                    <label for="fw-<?php echo e($index); ?>" class="label">Genişlik</label>
                                    <select id="fw-<?php echo e($index); ?>" wire:model="fields.<?php echo e($index); ?>.width" class="field">
                                        <option value="50">Yarım satır</option>
                                        <option value="100">Tam satır</option>
                                    </select>
                                </div>
                            </div>

                            <label class="flex items-center gap-2 text-sm text-ink-700">
                                <input type="checkbox" wire:model="fields.<?php echo e($index); ?>.required"
                                       class="size-4 rounded border-ink-300 text-brand-600">
                                Doldurulması zorunlu
                            </label>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>
    </div>

    <div class="space-y-4">
        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Form ayarları</h2>

            <div>
                <label for="fs-name" class="label">Form adı</label>
                <input id="fs-name" type="text" wire:model="settings.name"
                       class="field <?php $__errorArgs = ['settings.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['settings.name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <label for="fs-btn-<?php echo e($locale->code); ?>" class="label">
                        Buton yazısı
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locales->count() > 1): ?> <span class="font-normal text-ink-400">(<?php echo e($locale->code); ?>)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                    <input id="fs-btn-<?php echo e($locale->code); ?>" type="text" wire:model="settings.submit_label.<?php echo e($locale->code); ?>" class="field">
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <label for="fs-ok-<?php echo e($locale->code); ?>" class="label">
                        Gönderim sonrası mesaj
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locales->count() > 1): ?> <span class="font-normal text-ink-400">(<?php echo e($locale->code); ?>)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </label>
                    <textarea id="fs-ok-<?php echo e($locale->code); ?>" rows="2" wire:model="settings.success_message.<?php echo e($locale->code); ?>" class="field"></textarea>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>

        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Bildirim</h2>

            <div>
                <label for="fs-emails" class="label">Bildirim e-postaları</label>
                <input id="fs-emails" type="text" wire:model="settings.notify_emails"
                       class="field <?php $__errorArgs = ['settings.notify_emails'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       placeholder="info@site.com, satis@site.com">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['settings.notify_emails'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="error"><?php echo e($message); ?></p>
                <?php else: ?>
                    <p class="hint">Birden fazla adresi virgülle ayırın. Boş bırakırsanız e-posta gönderilmez; mesajlar yine de panelde birikir.</p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <label class="flex items-start gap-2.5">
                <input type="checkbox" wire:model="settings.store_submissions" class="mt-0.5 size-4 rounded border-ink-300 text-brand-600">
                <span class="text-sm text-ink-700">
                    Mesajları panelde sakla
                    <span class="block text-xs text-ink-500">Kapatırsanız yalnızca e-posta gönderilir, kayıt tutulmaz.</span>
                </span>
            </label>

            <label class="flex items-start gap-2.5">
                <input type="checkbox" wire:model="settings.spam_protection" class="mt-0.5 size-4 rounded border-ink-300 text-brand-600">
                <span class="text-sm text-ink-700">
                    İstenmeyen gönderim koruması
                    <span class="block text-xs text-ink-500">Ziyaretçiye görünmeyen bir tuzak alanla botları eler. Açık kalmalı.</span>
                </span>
            </label>

            <div>
                <label for="fs-redirect" class="label">Gönderim sonrası yönlendirme</label>
                <input id="fs-redirect" type="text" wire:model="settings.redirect_url" class="field" placeholder="/tesekkurler">
                <p class="hint">Boş bırakırsanız aynı sayfada teşekkür mesajı gösterilir.</p>
            </div>
        </section>

        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.forms.index')); ?>" wire:navigate class="btn-ghost flex-1">Geri</a>
            <button type="submit" class="btn-primary flex-1">Kaydet</button>
        </div>
    </div>
</form>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/admin/forms/form-edit.blade.php ENDPATH**/ ?>