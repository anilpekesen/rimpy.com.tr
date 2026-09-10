<?php $locale = $context->locale; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $form): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($context->editing): ?>
        <p class="text-sm text-ink-400">Form seçilmedi. Sağdaki panelden bir form seçin.</p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <div class="frm" data-form>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($settings['title'])): ?>
            <h3 class="frm-title"><?php echo e($settings['title']); ?></h3>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($settings['description'])): ?>
            <p class="frm-desc"><?php echo e($settings['description']); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <form method="POST" action="<?php echo e(route('form.submit', $form->key)); ?>" class="frm-form">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="locale" value="<?php echo e($locale); ?>">
            <input type="hidden" name="page_url" value="<?php echo e(request()->fullUrl()); ?>">

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->spam_protection): ?>
                <div aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden">
                    <label>Bu alanı boş bırakın <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>
                <input type="hidden" name="_ts" value="<?php echo e(now()->timestamp); ?>">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="frm-grid">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $form->fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $name = $field['key'];
                        $label = $field['label'][$locale] ?? $field['label'][\App\Support\Locales::default()] ?? $name;
                        $required = ! empty($field['required']);
                        $inputId = "f-{$form->key}-{$name}";
                        $full = ($field['width'] ?? 100) >= 100;
                    ?>

                    <div class="frm-field <?php if($full): ?> frm-field--full <?php endif; ?>">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field['type'] === 'checkbox'): ?>
                            <label class="frm-check" for="<?php echo e($inputId); ?>">
                                <input class="frm-input" type="checkbox" id="<?php echo e($inputId); ?>" name="<?php echo e($name); ?>"
                                       value="1" <?php if($required): echo 'required'; endif; ?>>
                                <span><?php echo e($label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?> <span aria-hidden="true">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                            </label>
                        <?php else: ?>
                            <label class="frm-label" for="<?php echo e($inputId); ?>">
                                <?php echo e($label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($required): ?> <span aria-hidden="true">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </label>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($field['type']):
                                case ('textarea'): ?>
                                    <textarea class="frm-input" id="<?php echo e($inputId); ?>" name="<?php echo e($name); ?>"
                                              rows="<?php echo e($field['rows'] ?? 4); ?>" <?php if($required): echo 'required'; endif; ?>
                                              <?php if($required): ?> aria-required="true" <?php endif; ?>></textarea>
                                    <?php break; ?>

                                <?php case ('select'): ?>
                                    <select class="frm-input" id="<?php echo e($inputId); ?>" name="<?php echo e($name); ?>" <?php if($required): echo 'required'; endif; ?>>
                                        <option value="">—</option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $field['options'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($option['value'] ?? $option); ?>"><?php echo e($option['label'][$locale] ?? $option['value'] ?? $option); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                    <?php break; ?>

                                <?php case ('file'): ?>
                                    <input class="frm-input" type="file" id="<?php echo e($inputId); ?>" name="<?php echo e($name); ?>"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" <?php if($required): echo 'required'; endif; ?>>
                                    <?php break; ?>

                                <?php default: ?>
                                    <input class="frm-input" type="<?php echo e($field['type']); ?>" id="<?php echo e($inputId); ?>"
                                           name="<?php echo e($name); ?>" <?php if($required): echo 'required'; endif; ?>
                                           <?php if($required): ?> aria-required="true" <?php endif; ?>
                                           autocomplete="<?php echo e(['email' => 'email', 'tel' => 'tel'][$field['type']] ?? 'on'); ?>">
                            <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = [$name, 'form-'.$form->key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="frm-error" role="alert"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <button type="submit" class="btn-el frm-submit <?php if(($settings['button_width'] ?? 'auto') === 'full'): ?> btn-el--full <?php endif; ?>">
                <?php echo e($form->localized('submit_label', $locale) ?? 'Gönder'); ?>

            </button>
        </form>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('form_success') === $form->key): ?>
            <p class="frm-success" role="status"><?php echo e($form->localized('success_message', $locale)); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/builder/widgets/form.blade.php ENDPATH**/ ?>