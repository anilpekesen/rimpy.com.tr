<form wire:submit="save" class="grid gap-5 lg:grid-cols-[1fr_20rem]">

    <div class="min-w-0 space-y-5">
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
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['space-y-5', 'hidden' => $activeLocale !== $locale->code]); ?>">
                <section class="card-pad space-y-4">
                    <div>
                        <label for="i-title-<?php echo e($locale->code); ?>" class="label">Başlık</label>
                        <input id="i-title-<?php echo e($locale->code); ?>" type="text" wire:model="translations.<?php echo e($locale->code); ?>.title"
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
                        <label for="i-slug-<?php echo e($locale->code); ?>" class="label">Adres</label>
                        <div class="flex items-center gap-1">
                            <span class="shrink-0 font-mono text-xs text-ink-400">
                                <?php echo e($locales->count() > 1 ? '/'.$locale->code : ''); ?>/<?php echo e($type->prefix($locale->code)); ?>/
                            </span>
                            <input id="i-slug-<?php echo e($locale->code); ?>" type="text" wire:model="translations.<?php echo e($locale->code); ?>.slug"
                                   class="field font-mono text-xs" placeholder="otomatik">
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type->supports('excerpt')): ?>
                        <div>
                            <label for="i-exc-<?php echo e($locale->code); ?>" class="label">Kısa açıklama</label>
                            <textarea id="i-exc-<?php echo e($locale->code); ?>" rows="2" wire:model="translations.<?php echo e($locale->code); ?>.excerpt" class="field"></textarea>
                            <p class="hint">Listelerde başlığın altında görünen özet.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type->supports('body')): ?>
                        <div wire:ignore>
                            <span class="label">İçerik</span>
                            <div class="overflow-hidden rounded-lg border border-ink-300"
                                 x-data="{ cmd(c, v = null) { document.execCommand(c, false, v); $refs.ed.focus(); } }">
                                <div class="flex flex-wrap gap-0.5 border-b border-ink-200 bg-ink-50 p-1">
                                    <button type="button" @click="cmd('bold')" class="btn-ghost btn-sm font-bold">B</button>
                                    <button type="button" @click="cmd('italic')" class="btn-ghost btn-sm italic">I</button>
                                    <button type="button" @click="cmd('formatBlock','<h2>')" class="btn-ghost btn-sm">H2</button>
                                    <button type="button" @click="cmd('formatBlock','<h3>')" class="btn-ghost btn-sm">H3</button>
                                    <button type="button" @click="cmd('formatBlock','<p>')" class="btn-ghost btn-sm">P</button>
                                    <button type="button" @click="cmd('insertUnorderedList')" class="btn-ghost btn-sm">•</button>
                                    <button type="button" @click="cmd('insertOrderedList')" class="btn-ghost btn-sm">1.</button>
                                    <button type="button" @click="const u = prompt('Bağlantı adresi:'); if (u) cmd('createLink', u)" class="btn-ghost btn-sm">
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'link','class' => 'size-3.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'link','class' => 'size-3.5']); ?>
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
                                    <button type="button" @click="cmd('removeFormat')" class="btn-ghost btn-sm ml-auto">⌫</button>
                                </div>
                                <div x-ref="ed" contenteditable="true"
                                     class="max-w-none px-4 py-3 text-sm focus:outline-none [&_h2]:mt-4 [&_h2]:mb-2 [&_h2]:text-lg [&_h2]:font-semibold [&_h3]:mt-3 [&_h3]:mb-1.5 [&_h3]:font-semibold [&_p]:mb-3 [&_ul]:mb-3 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:mb-3 [&_ol]:list-decimal [&_ol]:pl-5 [&_a]:text-brand-600 [&_a]:underline"
                                     style="min-height: 280px"
                                     x-on:input.debounce.600ms="$wire.set('translations.<?php echo e($locale->code); ?>.body', $refs.ed.innerHTML, false)"
                                ><?php echo $translations[$locale->code]['body'] ?? ''; ?></div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </section>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type->fields): ?>
                    <section class="card-pad space-y-4">
                        <h2 class="text-sm font-semibold text-ink-900"><?php echo e($type->label('tr')); ?> bilgileri</h2>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $type->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $fieldLabel = $field['label'][$locale->code] ?? $field['label'][$locales->first()->code] ?? $field['key']; ?>
                            <div>
                                <label for="cf-<?php echo e($locale->code); ?>-<?php echo e($field['key']); ?>" class="label">
                                    <?php echo e($fieldLabel); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($field['required'])): ?> <span class="text-red-600" aria-hidden="true">*</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field['type'] === 'textarea'): ?>
                                    <textarea id="cf-<?php echo e($locale->code); ?>-<?php echo e($field['key']); ?>" rows="3"
                                              wire:model="translations.<?php echo e($locale->code); ?>.custom.<?php echo e($field['key']); ?>"
                                              class="field <?php $__errorArgs = ['translations.'.$locale->code.'.custom.'.$field['key']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"></textarea>
                                <?php else: ?>
                                    <input id="cf-<?php echo e($locale->code); ?>-<?php echo e($field['key']); ?>" type="<?php echo e($field['type']); ?>"
                                           wire:model="translations.<?php echo e($locale->code); ?>.custom.<?php echo e($field['key']); ?>"
                                           class="field <?php $__errorArgs = ['translations.'.$locale->code.'.custom.'.$field['key']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['translations.'.$locale->code.'.custom.'.$field['key']];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="error"><?php echo e($fieldLabel); ?> zorunludur.</p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type->supports('seo')): ?>
                    <section class="card-pad space-y-4">
                        <h2 class="text-sm font-semibold text-ink-900">Arama motoru</h2>
                        <div>
                            <label for="i-st-<?php echo e($locale->code); ?>" class="label">SEO başlığı</label>
                            <input id="i-st-<?php echo e($locale->code); ?>" type="text" wire:model="translations.<?php echo e($locale->code); ?>.seo_title"
                                   class="field" maxlength="70" placeholder="Boşsa başlık kullanılır">
                        </div>
                        <div>
                            <label for="i-sd-<?php echo e($locale->code); ?>" class="label">SEO açıklaması</label>
                            <textarea id="i-sd-<?php echo e($locale->code); ?>" rows="2" wire:model.live="translations.<?php echo e($locale->code); ?>.seo_description"
                                      class="field" maxlength="320"></textarea>
                            <p class="hint"><?php echo e(mb_strlen($translations[$locale->code]['seo_description'] ?? '')); ?> / 160 karakter</p>
                        </div>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="space-y-5">
        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Yayın</h2>

            <div>
                <label for="i-status" class="label">Durum</label>
                <select id="i-status" wire:model="form.status" class="field">
                    <option value="draft">Taslak</option>
                    <option value="published">Yayında</option>
                    <option value="scheduled">Zamanlanmış</option>
                </select>
            </div>

            <div>
                <label for="i-date" class="label">Yayın tarihi</label>
                <input id="i-date" type="datetime-local" wire:model="form.published_at" class="field">
            </div>

            <label class="flex items-center gap-2 text-sm text-ink-700">
                <input type="checkbox" wire:model="form.is_featured" class="size-4 rounded border-ink-300 text-brand-600">
                Öne çıkar
            </label>

            <div class="flex gap-2 border-t border-ink-100 pt-4">
                <button type="submit" class="btn-primary flex-1">Kaydet</button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type->supports('builder')): ?>
                    <a href="<?php echo e(route('admin.builder.content', $item->id)); ?>" class="btn-secondary" title="Serbest tasarım">
                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'blocks','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'blocks','class' => 'size-4']); ?>
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
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </section>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type->supports('image')): ?>
            <section class="card-pad">
                <h2 class="mb-3 text-sm font-semibold text-ink-900">Kapak görseli</h2>
                <?php $cover = \App\Models\Media::find($form['featured_image_id'] ?? null); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cover): ?>
                    <img src="<?php echo e($cover->url('small')); ?>" alt="" class="mb-2 w-full rounded-lg object-cover">
                    <button type="button" wire:click="$set('form.featured_image_id', null)"
                            class="btn-ghost btn-sm w-full text-red-600">Kaldır</button>
                <?php else: ?>
                    <button type="button" class="btn-secondary btn-sm w-full"
                            @click="$dispatch('open-media-picker', { target: 'form.featured_image_id' })"
                            @media-applied.stop="$wire.set('form.featured_image_id', $event.detail.mediaId)">
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
<?php endif; ?> Görsel seç
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type->supports('taxonomies') && $type->taxonomies->isNotEmpty()): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $type->taxonomies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxonomy): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <section class="card-pad">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-ink-900"><?php echo e($taxonomy->pluralLabel('tr')); ?></h2>
                        <a href="<?php echo e(route('admin.terms.index', [$type->key, $taxonomy->key])); ?>" wire:navigate
                           class="text-xs font-medium text-brand-600 hover:underline">Yönet</a>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($taxonomy->terms->isEmpty()): ?>
                        <p class="text-xs text-ink-400">Henüz tanımlı değil.</p>
                    <?php else: ?>
                        <div class="max-h-52 space-y-1.5 overflow-y-auto">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $taxonomy->terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center gap-2 text-sm text-ink-700">
                                    <input type="checkbox" wire:model="selectedTerms" value="<?php echo e($term->id); ?>"
                                           class="size-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
                                    <?php echo e($term->t('name')); ?>

                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </section>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</form>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/admin/content/item-edit.blade.php ENDPATH**/ ?>