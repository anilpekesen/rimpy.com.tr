<div class="card p-6">
    <div class="mb-5">
        <h2 class="text-sm font-semibold text-ink-900">Yeni şifre belirleyin</h2>
        <p class="mt-1 text-sm text-ink-500">
            En az {{ config('cms.security.min_password_length') }} karakter, harf ve rakam içermeli.
        </p>
    </div>

    <form wire:submit="updatePassword" class="space-y-4">
        @error('email')
            <div role="alert" class="rounded-lg bg-red-50 px-3 py-2.5 text-sm text-red-700 ring-1 ring-red-600/15 ring-inset">
                {{ $message }}
            </div>
        @enderror

        <div>
            <label for="email" class="label">E-posta adresi</label>
            <input id="email" type="email" wire:model="email" class="field" autocomplete="username" required>
        </div>

        <div>
            <label for="password" class="label">Yeni şifre</label>
            <input id="password" type="password" wire:model="password"
                   class="field @error('password') field-error @enderror"
                   autocomplete="new-password" autofocus required>
            @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="password_confirmation" class="label">Yeni şifre (tekrar)</label>
            <input id="password_confirmation" type="password" wire:model="password_confirmation"
                   class="field" autocomplete="new-password" required>
        </div>

        <button type="submit" class="btn-primary w-full">Şifreyi güncelle</button>
    </form>
</div>
