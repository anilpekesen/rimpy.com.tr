<form wire:submit="save" class="max-w-3xl space-y-5">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locales->count() > 1): ?>
        <div class="flex rounded-lg border border-ink-200 bg-white p-0.5 text-sm">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" wire:click="$set('activeLocale', '<?php echo e($locale->code); ?>')"
                        class="rounded-md px-3 py-1.5 font-medium transition
                               <?php echo e($activeLocale === $locale->code ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-ink-100'); ?>">
                    <?php echo e($locale->native_name); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['space-y-4', 'hidden' => $activeLocale !== $locale->code]); ?>">
            <section class="card-pad space-y-4">
                <h2 class="text-sm font-semibold text-ink-900">Sayfa bilgileri</h2>

                <div>
                    <label for="t-<?php echo e($locale->code); ?>" class="label">Başlık</label>
                    <input id="t-<?php echo e($locale->code); ?>" type="text" wire:model="translations.<?php echo e($locale->code); ?>.title"
                           class="field <?php $__errorArgs = ['translations.'.$locale->code.'.title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['translations.'.$locale->code.'.title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div>
                    <label for="s-<?php echo e($locale->code); ?>" class="label">Adres</label>
                    <div class="flex items-center gap-1">
                        <span class="shrink-0 font-mono text-xs text-ink-400">
                            <?php echo e($locales->count() > 1 ? '/'.$locale->code : ''); ?>/<?php echo e($page->parent?->t('slug', $locale->code) ? $page->parent->t('slug', $locale->code).'/' : ''); ?>

                        </span>
                        <input id="s-<?php echo e($locale->code); ?>" type="text" wire:model="translations.<?php echo e($locale->code); ?>.slug"
                               class="field font-mono text-xs" placeholder="otomatik">
                    </div>
                    <p class="hint">Boş bırakırsanız başlıktan otomatik üretilir. Yayındaki bir sayfanın adresini
                        değiştirirseniz eski adres için bir yönlendirme eklemeyi unutmayın.</p>
                </div>

                <div>
                    <label for="e-<?php echo e($locale->code); ?>" class="label">Kısa açıklama</label>
                    <textarea id="e-<?php echo e($locale->code); ?>" rows="2" wire:model="translations.<?php echo e($locale->code); ?>.excerpt" class="field"></textarea>
                    <p class="hint">Sayfa listelerinde ve paylaşımlarda kullanılır.</p>
                </div>
            </section>

            <section class="card-pad space-y-4">
                <h2 class="text-sm font-semibold text-ink-900">Arama motoru</h2>

                <div>
                    <label for="st-<?php echo e($locale->code); ?>" class="label">SEO başlığı</label>
                    <input id="st-<?php echo e($locale->code); ?>" type="text" wire:model="translations.<?php echo e($locale->code); ?>.seo_title"
                           class="field" maxlength="70" placeholder="Boşsa sayfa başlığı kullanılır">
                </div>

                <div>
                    <label for="sd-<?php echo e($locale->code); ?>" class="label">SEO açıklaması</label>
                    <textarea id="sd-<?php echo e($locale->code); ?>" rows="2" wire:model.live="translations.<?php echo e($locale->code); ?>.seo_description"
                              class="field <?php $__errorArgs = ['translations.'.$locale->code.'.seo_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" maxlength="320"></textarea>
                    <p class="hint"><?php echo e(mb_strlen($translations[$locale->code]['seo_description'] ?? '')); ?> / 160 karakter</p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['translations.'.$locale->code.'.seo_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <label class="flex items-start gap-2.5">
                    <input type="checkbox" wire:model="translations.<?php echo e($locale->code); ?>.noindex"
                           class="mt-0.5 size-4 rounded border-ink-300 text-brand-600">
                    <span class="text-sm text-ink-700">
                        Bu sayfayı arama motorlarına gösterme
                        <span class="block text-xs text-ink-500">Teşekkür sayfası, iç duyuru gibi sayfalar için.</span>
                    </span>
                </label>
            </section>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <section class="card-pad space-y-4">
        <h2 class="text-sm font-semibold text-ink-900">Yayın</h2>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="p-status" class="label">Durum</label>
                <select id="p-status" wire:model.live="form.status" class="field">
                    <option value="draft">Taslak — yalnızca siz görürsünüz</option>
                    <option value="published">Yayında — herkes görür</option>
                    <option value="scheduled">Zamanlanmış</option>
                </select>
            </div>

            <div>
                <label for="p-date" class="label">Yayın tarihi</label>
                <input id="p-date" type="datetime-local" wire:model="form.published_at" class="field">
                <p class="hint">İleri bir tarih girerseniz sayfa o zamana kadar görünmez.</p>
            </div>

            <div>
                <label for="p-parent" class="label">Üst sayfa</label>
                <select id="p-parent" wire:model="form.parent_id"
                        class="field <?php $__errorArgs = ['form.parent_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">— Yok (en üst seviye) —</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($parent->id); ?>"><?php echo e($parent->t('title')); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['form.parent_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="error"><?php echo e($message); ?></p>
                <?php else: ?>
                    <p class="hint">Seçerseniz adres üst sayfanın altına iner: /kurumsal/tarihce</p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div>
                <label for="p-sort" class="label">Sıra</label>
                <input id="p-sort" type="number" min="0" wire:model="form.sort" class="field">
            </div>
        </div>

        <label class="flex items-start gap-2.5">
            <input type="checkbox" wire:model="form.show_in_search"
                   class="mt-0.5 size-4 rounded border-ink-300 text-brand-600">
            <span class="text-sm text-ink-700">Site haritasına dahil et</span>
        </label>
    </section>

    <div class="flex justify-between">
        <a href="<?php echo e(route('admin.builder.page', $page)); ?>" class="btn-secondary">
            <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'edit','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit','class' => 'size-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?> Tasarımı düzenle
        </a>
        <div class="flex gap-2">
            <a href="<?php echo e(route('admin.pages.index')); ?>" wire:navigate class="btn-ghost">Vazgeç</a>
            <button type="submit" class="btn-primary">Kaydet</button>
        </div>
    </div>
</form>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/admin/pages/page-settings.blade.php ENDPATH**/ ?>