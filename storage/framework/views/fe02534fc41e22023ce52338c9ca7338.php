<div class="max-w-3xl space-y-5">

    <div class="card-pad">
        <p class="text-sm text-ink-600">
            Buradaki renk ve yazı tipleri sitenin tamamına uygulanır. Kurucudaki bir öğeye
            <strong>tema rengi</strong> seçtiyseniz, burada rengi değiştirdiğinizde o öğe de birlikte değişir.
        </p>
    </div>

    <section class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">Hazır renk şemaları</h2>
            <p class="mt-0.5 text-xs text-ink-500">Bir şema seçip ardından tek tek ince ayar yapabilirsiniz.</p>
        </div>

        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $presets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $preset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button" wire:click="applyPreset('<?php echo e($key); ?>')"
                        class="rounded-lg border border-ink-200 p-3 text-left transition hover:border-brand-400 hover:bg-brand-50">
                    <span class="flex gap-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['primary', 'secondary', 'accent', 'surface']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="size-6 rounded" style="background: <?php echo e($preset['colors'][$token]); ?>"></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <span class="mt-2 block text-xs font-medium text-ink-700"><?php echo e($preset['label']); ?></span>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </section>

    <form wire:submit="save" class="space-y-5">
        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Renkler</h2>

            <div class="grid gap-3 sm:grid-cols-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                    'primary' => ['Ana renk', 'Butonlar, bağlantılar ve vurgular.'],
                    'secondary' => ['İkincil renk', 'Koyu zeminler ve başlık alanları.'],
                    'accent' => ['Vurgu rengi', 'Dikkat çekilecek küçük öğeler.'],
                    'heading' => ['Başlık rengi', null],
                    'text' => ['Metin rengi', null],
                    'muted' => ['Soluk metin', 'Tarih, alt yazı gibi ikincil bilgiler.'],
                    'background' => ['Sayfa zemini', null],
                    'surface' => ['Kart zemini', 'Bölüm ve kartların açık zemini.'],
                    'border' => ['Çizgi rengi', null],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $token => [$label, $hint]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div>
                        <label for="c-<?php echo e($token); ?>" class="label"><?php echo e($label); ?></label>
                        <div class="flex items-center gap-2">
                            <input id="c-<?php echo e($token); ?>" type="color" wire:model.live="theme.colors.<?php echo e($token); ?>"
                                   class="size-9 shrink-0 cursor-pointer rounded border border-ink-300 bg-white p-0.5">
                            <input type="text" wire:model.live="theme.colors.<?php echo e($token); ?>"
                                   class="field font-mono text-xs <?php $__errorArgs = ['theme.colors.'.$token];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   aria-label="<?php echo e($label); ?> kodu">
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['theme.colors.'.$token];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="error"><?php echo e($message); ?></p>
                        <?php elseif($hint): ?>
                            <p class="hint"><?php echo e($hint); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="rounded-lg border border-ink-200 p-4"
                 style="background: <?php echo e($theme['colors']['surface']); ?>">
                <p class="text-xs font-semibold tracking-wide uppercase" style="color: <?php echo e($theme['colors']['primary']); ?>">Önizleme</p>
                <p class="mt-1.5 text-xl font-semibold" style="color: <?php echo e($theme['colors']['heading']); ?>">Örnek bir başlık</p>
                <p class="mt-1 text-sm" style="color: <?php echo e($theme['colors']['text']); ?>">
                    Bu paragraf metin rengiyle yazıldı. <span style="color: <?php echo e($theme['colors']['muted']); ?>">Bu kısım soluk renkte.</span>
                </p>
                <span class="mt-3 inline-block rounded-lg px-4 py-2 text-sm font-medium"
                      style="background: <?php echo e($theme['colors']['primary']); ?>; color: #fff; border-radius: <?php echo e($theme['layout']['radius']); ?>px">
                    Örnek buton
                </span>
            </div>
        </section>

        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Yazı tipleri</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="f-heading" class="label">Başlık yazı tipi</label>
                    <select id="f-heading" wire:model="theme.fonts.heading.family" class="field">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fonts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $family => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($family); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label for="f-body" class="label">Metin yazı tipi</label>
                    <select id="f-body" wire:model="theme.fonts.body.family" class="field">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fonts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $family => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($family); ?>"><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
                <div>
                    <label for="t-base" class="label">Temel yazı boyutu (px)</label>
                    <input id="t-base" type="number" min="12" max="22" wire:model="theme.typography.base_size"
                           class="field <?php $__errorArgs = ['theme.typography.base_size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['theme.typography.base_size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="t-lh" class="label">Satır yüksekliği</label>
                    <input id="t-lh" type="number" step="0.05" min="1.2" max="2.2" wire:model="theme.typography.line_height" class="field">
                </div>
            </div>
        </section>

        <section class="card-pad space-y-4">
            <h2 class="text-sm font-semibold text-ink-900">Düzen</h2>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="l-container" class="label">İçerik genişliği (px)</label>
                    <input id="l-container" type="number" min="600" max="1920" step="20" wire:model="theme.layout.container"
                           class="field <?php $__errorArgs = ['theme.layout.container'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['theme.layout.container'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="error"><?php echo e($message); ?></p>
                    <?php else: ?>
                        <p class="hint">Metnin sayfada kapladığı en fazla genişlik. 1140–1280 arası yaygındır.</p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="l-radius" class="label">Köşe yuvarlaklığı (px)</label>
                    <input id="l-radius" type="number" min="0" max="40" wire:model="theme.layout.radius"
                           class="field <?php $__errorArgs = ['theme.layout.radius'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['theme.layout.radius'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="error"><?php echo e($message); ?></p>
                    <?php else: ?>
                        <p class="hint">0 keskin, 16+ yumuşak bir görünüm verir.</p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="l-section" class="label">Bölüm dikey boşluğu (px)</label>
                    <input id="l-section" type="number" min="0" max="200" step="8" wire:model="theme.layout.section_padding" class="field">
                </div>
                <div>
                    <label for="l-gutter" class="label">Kenar boşluğu (px)</label>
                    <input id="l-gutter" type="number" min="8" max="80" wire:model="theme.layout.gutter" class="field">
                </div>
            </div>
        </section>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Temayı kaydet</button>
        </div>
    </form>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/admin/design/theme-settings.blade.php ENDPATH**/ ?>