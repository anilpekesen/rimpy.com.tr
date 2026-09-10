<form wire:submit="save" class="max-w-3xl space-y-5">

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">Site kimliği</h2>
            <p class="mt-0.5 text-xs text-ink-500">Tarayıcı sekmesinde, arama sonuçlarında ve panelde görünen bilgiler.</p>
        </div>

        <div>
            <label for="site-name" class="label">Site adı</label>
            <input id="site-name" type="text" wire:model="general.site_name"
                   class="field <?php $__errorArgs = ['general.site_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['general.site_name'];
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
                <label for="tagline-<?php echo e($locale->code); ?>" class="label">
                    Slogan <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locales->count() > 1): ?> <span class="font-normal text-ink-400">(<?php echo e($locale->native_name); ?>)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <input id="tagline-<?php echo e($locale->code); ?>" type="text" wire:model="general.tagline.<?php echo e($locale->code); ?>" class="field">
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <p class="hint">Slogan, site adının yanında kısa bir açıklama olarak kullanılır.</p>

        <div class="grid gap-4 sm:grid-cols-3">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                'logo_id' => ['Logo', 'Açık zeminde kullanılan ana logo.'],
                'logo_light_id' => ['Açık renkli logo', 'Koyu zeminlerde kullanılır.'],
                'favicon_id' => ['Sekme simgesi', 'Kare, en az 64×64 piksel.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => [$label, $hint]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $media = \App\Models\Media::find($general[$field] ?? null); ?>
                <div>
                    <span class="label"><?php echo e($label); ?></span>
                    <div class="rounded-lg border border-ink-200 p-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($media): ?>
                            <img src="<?php echo e($media->url('small')); ?>" alt="" class="mx-auto mb-2 h-12 object-contain">
                            <button type="button" wire:click="$set('general.<?php echo e($field); ?>', null)"
                                    class="btn-ghost btn-sm w-full text-red-600">Kaldır</button>
                        <?php else: ?>
                            <button type="button" class="btn-secondary btn-sm w-full"
                                    @click="$dispatch('open-media-picker', { target: 'general.<?php echo e($field); ?>' })"
                                    @media-applied.stop="$wire.set('general.<?php echo e($field); ?>', $event.detail.mediaId)">
                                Seç
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="hint"><?php echo e($hint); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">İletişim bilgileri</h2>
            <p class="mt-0.5 text-xs text-ink-500">Sitenin alt bilgisinde ve iletişim sayfasında kullanılır.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="c-email" class="label">E-posta</label>
                <input id="c-email" type="email" wire:model="contact.email"
                       class="field <?php $__errorArgs = ['contact.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['contact.email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label for="c-phone" class="label">Telefon</label>
                <input id="c-phone" type="tel" wire:model="contact.phone" class="field">
            </div>
            <div>
                <label for="c-wa" class="label">WhatsApp numarası</label>
                <input id="c-wa" type="tel" wire:model="contact.whatsapp" class="field" placeholder="905550000000">
                <p class="hint">Başında ülke kodu, boşluksuz.</p>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div>
                <label for="addr-<?php echo e($locale->code); ?>" class="label">
                    Adres <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locales->count() > 1): ?> <span class="font-normal text-ink-400">(<?php echo e($locale->native_name); ?>)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </label>
                <textarea id="addr-<?php echo e($locale->code); ?>" rows="2" wire:model="contact.address.<?php echo e($locale->code); ?>" class="field"></textarea>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">Sosyal medya</h2>
            <p class="mt-0.5 text-xs text-ink-500">Boş bıraktığınız hesaplar sitede hiç görünmez.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['facebook' => 'Facebook', 'instagram' => 'Instagram', 'linkedin' => 'LinkedIn', 'x' => 'X (Twitter)', 'youtube' => 'YouTube']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <label for="s-<?php echo e($key); ?>" class="label"><?php echo e($label); ?></label>
                    <input id="s-<?php echo e($key); ?>" type="url" wire:model="social.<?php echo e($key); ?>"
                           class="field <?php $__errorArgs = ['social.'.$key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="https://">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['social.'.$key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <div class="flex justify-end gap-2">
        <button type="submit" class="btn-primary">
            <span wire:loading.remove wire:target="save">Kaydet</span>
            <span wire:loading wire:target="save">Kaydediliyor…</span>
        </button>
    </div>
</form>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/admin/settings/general.blade.php ENDPATH**/ ?>