<form wire:submit="save" class="max-w-3xl space-y-5">

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">Varsayılan arama motoru bilgileri</h2>
            <p class="mt-0.5 text-xs text-ink-500">
                Kendi başlığı girilmemiş sayfalarda bu değerler kullanılır.
            </p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="space-y-3 rounded-lg border border-ink-200 p-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($locales->count() > 1): ?>
                    <p class="text-xs font-semibold tracking-wide text-ink-500 uppercase"><?php echo e($locale->native_name); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div>
                    <label for="mt-<?php echo e($locale->code); ?>" class="label">Başlık</label>
                    <input id="mt-<?php echo e($locale->code); ?>" type="text" wire:model="seo.meta_title.<?php echo e($locale->code); ?>"
                           class="field" maxlength="70">
                    <p class="hint">Google genellikle ilk 60 karakteri gösterir.</p>
                </div>

                <div>
                    <label for="md-<?php echo e($locale->code); ?>" class="label">Açıklama</label>
                    <textarea id="md-<?php echo e($locale->code); ?>" rows="2" wire:model.live="seo.meta_description.<?php echo e($locale->code); ?>"
                              class="field <?php $__errorArgs = ['seo.meta_description.'.$locale->code];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" maxlength="320"></textarea>
                    <p class="hint">
                        <?php echo e(mb_strlen($seo['meta_description'][$locale->code] ?? '')); ?> / 160 karakter —
                        arama sonucunda başlığın altında görünen metin.
                    </p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['seo.meta_description.'.$locale->code];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php $og = \App\Models\Media::find($seo['og_image_id'] ?? null); ?>
        <div>
            <span class="label">Paylaşım görseli</span>
            <div class="flex items-center gap-3 rounded-lg border border-ink-200 p-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($og): ?>
                    <img src="<?php echo e($og->url('small')); ?>" alt="" class="h-16 w-28 rounded object-cover">
                    <button type="button" wire:click="$set('seo.og_image_id', null)" class="btn-ghost btn-sm text-red-600">Kaldır</button>
                <?php else: ?>
                    <button type="button" class="btn-secondary btn-sm"
                            @click="$dispatch('open-media-picker', { target: 'seo.og_image_id' })"
                            @media-applied.stop="$wire.set('seo.og_image_id', $event.detail.mediaId)">Görsel seç</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <p class="hint">Site bağlantısı WhatsApp ya da LinkedIn'de paylaşıldığında görünen görsel. 1200×630 piksel önerilir.</p>
        </div>
    </section>

    <section class="card-pad space-y-4">
        <h2 class="text-sm font-semibold text-ink-900">Araç bağlantıları</h2>

        <div>
            <label for="ga" class="label">Google Analytics ölçüm kimliği</label>
            <input id="ga" type="text" wire:model="seo.google_analytics" class="field font-mono text-xs" placeholder="G-XXXXXXXXXX">
            <p class="hint">Google Analytics hesabınızdaki “Ölçüm Kimliği”. Boş bırakırsanız hiçbir izleme kodu yüklenmez.</p>
        </div>

        <div>
            <label for="gv" class="label">Google Search Console doğrulama kodu</label>
            <input id="gv" type="text" wire:model="seo.google_verification" class="field font-mono text-xs">
            <p class="hint">Search Console'un istediği “content” değerini yapıştırın.</p>
        </div>
    </section>

    <section class="card-pad">
        <label class="flex items-start gap-3">
            <input type="checkbox" wire:model="seo.noindex_site"
                   class="mt-0.5 size-4 rounded border-ink-300 text-red-600 focus:ring-red-500">
            <span>
                <span class="block text-sm font-medium text-ink-900">Siteyi arama motorlarından gizle</span>
                <span class="block text-xs text-ink-500">
                    Site henüz hazır değilken açın. <strong class="text-red-700">Yayına aldıktan sonra kapatmayı unutmayın</strong> —
                    açık kaldığı sürece siteniz Google'da hiç görünmez.
                </span>
            </span>
        </label>
    </section>

    <div class="flex justify-end">
        <button type="submit" class="btn-primary">Kaydet</button>
    </div>
</form>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/admin/settings/seo.blade.php ENDPATH**/ ?>