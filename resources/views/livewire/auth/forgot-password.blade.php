<div class="card p-6">
    @if ($sent)
        <div class="text-center">
            <div class="mx-auto mb-3 flex size-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                <x-icon name="check"/>
            </div>
            <h2 class="text-sm font-semibold text-ink-900">Bağlantı gönderildi</h2>
            <p class="mt-1.5 text-sm text-ink-500">
                Bu adres kayıtlıysa, şifre yenileme bağlantısı e-postanıza gönderildi.
                Gelen kutunuzu ve gereksiz posta klasörünü kontrol edin.
            </p>
            <a href="{{ route('admin.login') }}" wire:navigate class="btn-secondary mt-5 w-full">
                Giriş ekranına dön
            </a>
        </div>
    @else
        <div class="mb-5">
            <h2 class="text-sm font-semibold text-ink-900">Şifremi unuttum</h2>
            <p class="mt-1 text-sm text-ink-500">
                E-posta adresinizi girin, yeni şifre belirlemeniz için size bir bağlantı gönderelim.
            </p>
        </div>

        <form wire:submit="send" class="space-y-4">
            <div>
                <label for="email" class="label">E-posta adresi</label>
                <input id="email" type="email" wire:model="email"
                       class="field @error('email') field-error @enderror"
                       autocomplete="username" autofocus required>
                @error('email') <p class="error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full">Bağlantı gönder</button>

            <a href="{{ route('admin.login') }}" wire:navigate
               class="block text-center text-xs font-medium text-brand-600 hover:underline">
                Giriş ekranına dön
            </a>
        </form>
    @endif
</div>
