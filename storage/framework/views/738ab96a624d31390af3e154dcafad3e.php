<div class="max-w-2xl space-y-5">

    <form wire:submit="saveProfile" class="card-pad space-y-4">
        <h2 class="text-sm font-semibold text-ink-900">Bilgilerim</h2>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="a-name" class="label">Ad soyad</label>
                <input id="a-name" type="text" wire:model="name" class="field <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label for="a-email" class="label">E-posta</label>
                <input id="a-email" type="email" wire:model="email" class="field <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Kaydet</button>
        </div>
    </form>

    <form wire:submit="changePassword" class="card-pad space-y-4">
        <div>
            <h2 class="text-sm font-semibold text-ink-900">Şifre değiştir</h2>
            <p class="mt-0.5 text-xs text-ink-500">
                En az <?php echo e(config('cms.security.min_password_length')); ?> karakter, harf ve rakam içermeli.
                Başka bir sitede kullandığınız şifreyi burada kullanmayın.
            </p>
        </div>

        <div>
            <label for="a-current" class="label">Mevcut şifreniz</label>
            <input id="a-current" type="password" wire:model="currentPassword"
                   class="field <?php $__errorArgs = ['currentPassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="current-password">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['currentPassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="a-new" class="label">Yeni şifre</label>
                <input id="a-new" type="password" wire:model="newPassword"
                       class="field <?php $__errorArgs = ['newPassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" autocomplete="new-password">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newPassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div>
                <label for="a-new2" class="label">Yeni şifre (tekrar)</label>
                <input id="a-new2" type="password" wire:model="newPasswordConfirmation" class="field" autocomplete="new-password">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">Şifreyi değiştir</button>
        </div>
    </form>

    <section class="card-pad space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-sm font-semibold text-ink-900">İki adımlı doğrulama</h2>
                <p class="mt-0.5 text-sm text-ink-600">
                    Girişte, şifrenizin yanı sıra telefonunuzdaki uygulamadan bir kod istenir.
                    Şifreniz başkasının eline geçse bile hesabınıza giremez.
                </p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->hasTwoFactor()): ?>
                <span class="badge-green shrink-0">Açık</span>
            <?php else: ?>
                <span class="badge-amber shrink-0">Kapalı</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recoveryCodes): ?>
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 p-4">
                <p class="text-sm font-medium text-emerald-900">Kurtarma kodlarınız</p>
                <p class="mt-1 text-sm text-emerald-800">
                    Telefonunuza erişemediğinizde bu kodlarla giriş yapabilirsiniz. Her kod bir kez kullanılır.
                    <strong>Bir yere kaydedin — bu ekran bir daha gösterilmeyecek.</strong>
                </p>
                <ul class="mt-3 grid grid-cols-2 gap-1.5 font-mono text-sm text-emerald-900">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recoveryCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="rounded bg-white px-2 py-1"><?php echo e($code); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
                <button wire:click="$set('recoveryCodes', [])" class="btn-secondary btn-sm mt-3">Kaydettim</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settingUpTwoFactor): ?>
            <div class="space-y-4 rounded-lg border border-ink-200 p-4">
                <ol class="space-y-3 text-sm text-ink-700">
                    <li>
                        <strong>1.</strong> Telefonunuza bir doğrulama uygulaması kurun —
                        Google Authenticator, Microsoft Authenticator ya da Authy.
                    </li>
                    <li>
                        <strong>2.</strong> Uygulamayı açıp aşağıdaki kare kodu okutun.
                        <div class="mt-2 inline-block rounded-lg border border-ink-200 bg-white p-3">
                            <?php echo $qrCode; ?>

                        </div>
                        <p class="mt-1.5 text-xs text-ink-500">
                            Kod okutamıyorsanız bu anahtarı elle girin:
                            <span class="font-mono select-all"><?php echo e($pendingSecret); ?></span>
                        </p>
                    </li>
                    <li>
                        <strong>3.</strong> Uygulamada görünen 6 haneli kodu buraya yazın.
                        <div class="mt-2 flex gap-2">
                            <input type="text" wire:model="twoFactorCode" maxlength="6" inputmode="numeric"
                                   class="field w-32 text-center text-lg tracking-[0.3em] tabular-nums <?php $__errorArgs = ['twoFactorCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> field-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   aria-label="Doğrulama kodu">
                            <button wire:click="confirmTwoFactor" class="btn-primary">Doğrula ve aç</button>
                            <button wire:click="$set('settingUpTwoFactor', false)" class="btn-ghost">Vazgeç</button>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['twoFactorCode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </li>
                </ol>
            </div>
        <?php elseif($user->hasTwoFactor()): ?>
            <button wire:click="disableTwoFactor"
                    wire:confirm="İki adımlı doğrulama kapatılacak ve hesabınız yalnızca şifreyle korunacak. Devam edilsin mi?"
                    class="btn-secondary">Kapat</button>
        <?php else: ?>
            <button wire:click="startTwoFactor" class="btn-primary">Şimdi aç</button>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </section>

    <section class="card-pad">
        <h2 class="text-sm font-semibold text-ink-900">Son giriş</h2>
        <p class="mt-1 text-sm text-ink-600">
            <?php echo e($user->last_login_at?->format('d.m.Y H:i') ?? '—'); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->last_login_ip): ?>
                · <span class="font-mono text-xs"><?php echo e($user->last_login_ip); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
        <p class="mt-1 text-xs text-ink-500">
            Bu bilgiyi tanımıyorsanız şifrenizi hemen değiştirin ve site yöneticisine haber verin.
        </p>
    </section>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/mimar/resources/views/livewire/admin/account.blade.php ENDPATH**/ ?>