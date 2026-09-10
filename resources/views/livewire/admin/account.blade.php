<div class="max-w-2xl space-y-5">

    <form wire:submit="saveProfile" class="card-pad space-y-4">
        <h2 class="text-sm font-semibold text-ink-900">Bilgilerim</h2>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="a-name" class="label">Ad soyad</label>
                <input id="a-name" type="text" wire:model="name" class="field @error('name') field-error @enderror">
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="a-email" class="label">E-posta</label>
                <input id="a-email" type="email" wire:model="email" class="field @error('email') field-error @enderror">
                @error('email') <p class="error">{{ $message }}</p> @enderror
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
                En az {{ config('cms.security.min_password_length') }} karakter, harf ve rakam içermeli.
                Başka bir sitede kullandığınız şifreyi burada kullanmayın.
            </p>
        </div>

        <div>
            <label for="a-current" class="label">Mevcut şifreniz</label>
            <input id="a-current" type="password" wire:model="currentPassword"
                   class="field @error('currentPassword') field-error @enderror" autocomplete="current-password">
            @error('currentPassword') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="a-new" class="label">Yeni şifre</label>
                <input id="a-new" type="password" wire:model="newPassword"
                       class="field @error('newPassword') field-error @enderror" autocomplete="new-password">
                @error('newPassword') <p class="error">{{ $message }}</p> @enderror
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
            @if ($user->hasTwoFactor())
                <span class="badge-green shrink-0">Açık</span>
            @else
                <span class="badge-amber shrink-0">Kapalı</span>
            @endif
        </div>

        @if ($recoveryCodes)
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 p-4">
                <p class="text-sm font-medium text-emerald-900">Kurtarma kodlarınız</p>
                <p class="mt-1 text-sm text-emerald-800">
                    Telefonunuza erişemediğinizde bu kodlarla giriş yapabilirsiniz. Her kod bir kez kullanılır.
                    <strong>Bir yere kaydedin — bu ekran bir daha gösterilmeyecek.</strong>
                </p>
                <ul class="mt-3 grid grid-cols-2 gap-1.5 font-mono text-sm text-emerald-900">
                    @foreach ($recoveryCodes as $code)
                        <li class="rounded bg-white px-2 py-1">{{ $code }}</li>
                    @endforeach
                </ul>
                <button wire:click="$set('recoveryCodes', [])" class="btn-secondary btn-sm mt-3">Kaydettim</button>
            </div>
        @endif

        @if ($settingUpTwoFactor)
            <div class="space-y-4 rounded-lg border border-ink-200 p-4">
                <ol class="space-y-3 text-sm text-ink-700">
                    <li>
                        <strong>1.</strong> Telefonunuza bir doğrulama uygulaması kurun —
                        Google Authenticator, Microsoft Authenticator ya da Authy.
                    </li>
                    <li>
                        <strong>2.</strong> Uygulamayı açıp aşağıdaki kare kodu okutun.
                        <div class="mt-2 inline-block rounded-lg border border-ink-200 bg-white p-3">
                            {!! $qrCode !!}
                        </div>
                        <p class="mt-1.5 text-xs text-ink-500">
                            Kod okutamıyorsanız bu anahtarı elle girin:
                            <span class="font-mono select-all">{{ $pendingSecret }}</span>
                        </p>
                    </li>
                    <li>
                        <strong>3.</strong> Uygulamada görünen 6 haneli kodu buraya yazın.
                        <div class="mt-2 flex gap-2">
                            <input type="text" wire:model="twoFactorCode" maxlength="6" inputmode="numeric"
                                   class="field w-32 text-center text-lg tracking-[0.3em] tabular-nums @error('twoFactorCode') field-error @enderror"
                                   aria-label="Doğrulama kodu">
                            <button wire:click="confirmTwoFactor" class="btn-primary">Doğrula ve aç</button>
                            <button wire:click="$set('settingUpTwoFactor', false)" class="btn-ghost">Vazgeç</button>
                        </div>
                        @error('twoFactorCode') <p class="error">{{ $message }}</p> @enderror
                    </li>
                </ol>
            </div>
        @elseif ($user->hasTwoFactor())
            <button wire:click="disableTwoFactor"
                    wire:confirm="İki adımlı doğrulama kapatılacak ve hesabınız yalnızca şifreyle korunacak. Devam edilsin mi?"
                    class="btn-secondary">Kapat</button>
        @else
            <button wire:click="startTwoFactor" class="btn-primary">Şimdi aç</button>
        @endif
    </section>

    <section class="card-pad">
        <h2 class="text-sm font-semibold text-ink-900">Son giriş</h2>
        <p class="mt-1 text-sm text-ink-600">
            {{ $user->last_login_at?->format('d.m.Y H:i') ?? '—' }}
            @if ($user->last_login_ip)
                · <span class="font-mono text-xs">{{ $user->last_login_ip }}</span>
            @endif
        </p>
        <p class="mt-1 text-xs text-ink-500">
            Bu bilgiyi tanımıyorsanız şifrenizi hemen değiştirin ve site yöneticisine haber verin.
        </p>
    </section>
</div>
