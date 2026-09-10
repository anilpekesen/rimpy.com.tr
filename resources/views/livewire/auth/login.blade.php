<div class="card p-6">
    <form wire:submit="login" class="space-y-4">
        @error('email')
            <div role="alert" class="rounded-lg bg-red-50 px-3 py-2.5 text-sm text-red-700 ring-1 ring-red-600/15 ring-inset">
                {{ $message }}
            </div>
        @enderror

        <div>
            <label for="email" class="label">E-posta adresi</label>
            <input
                id="email"
                type="email"
                wire:model="email"
                class="field @error('email') field-error @enderror"
                autocomplete="username"
                autofocus
                required
            >
        </div>

        <div>
            <div class="mb-1.5 flex items-baseline justify-between">
                <label for="password" class="label mb-0">Şifre</label>
                <a href="{{ route('admin.password.request') }}" wire:navigate
                   class="text-xs font-medium text-brand-600 hover:text-brand-700 hover:underline">
                    Şifremi unuttum
                </a>
            </div>
            <input
                id="password"
                type="password"
                wire:model="password"
                class="field @error('password') field-error @enderror"
                autocomplete="current-password"
                required
            >
            @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-ink-600">
            <input type="checkbox" wire:model="remember"
                   class="size-4 rounded border-ink-300 text-brand-600 focus:ring-brand-500">
            Bu cihazda oturumum açık kalsın
        </label>

        <button type="submit" class="btn-primary w-full">
            <span wire:loading.remove wire:target="login">Giriş yap</span>
            <span wire:loading wire:target="login" class="flex items-center gap-2">
                <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/>
                </svg>
                Kontrol ediliyor…
            </span>
        </button>
    </form>
</div>
