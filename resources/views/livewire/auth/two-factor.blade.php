<div class="card p-6">
    <div class="mb-5">
        <h2 class="text-sm font-semibold text-ink-900">İki adımlı doğrulama</h2>
        <p class="mt-1 text-sm text-ink-500">
            {{ $useRecovery
                ? 'Kaydettiğiniz kurtarma kodlarından birini girin.'
                : 'Doğrulama uygulamanızdaki 6 haneli kodu girin.' }}
        </p>
    </div>

    <form wire:submit="verify" class="space-y-4">
        <div>
            <label for="code" class="label">{{ $useRecovery ? 'Kurtarma kodu' : 'Doğrulama kodu' }}</label>
            <input
                id="code"
                type="text"
                wire:model="code"
                class="field @error('code') field-error @enderror {{ $useRecovery ? '' : 'text-center text-lg tracking-[0.4em] tabular-nums' }}"
                inputmode="{{ $useRecovery ? 'text' : 'numeric' }}"
                autocomplete="one-time-code"
                maxlength="{{ $useRecovery ? 24 : 6 }}"
                autofocus
                required
            >
            @error('code') <p class="error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary w-full">Doğrula ve gir</button>
    </form>

    <button type="button" wire:click="$toggle('useRecovery')"
            class="mt-4 w-full text-center text-xs font-medium text-brand-600 hover:underline">
        {{ $useRecovery ? 'Uygulama kodunu kullan' : 'Telefonuma erişemiyorum, kurtarma kodu kullanacağım' }}
    </button>
</div>
