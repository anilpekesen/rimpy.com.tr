<?php

namespace App\Livewire\Admin;

use App\Services\ActivityLogger;
use App\Services\TwoFactor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Account extends Component
{
    public string $name = '';

    public string $email = '';

    public string $currentPassword = '';

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    // İki adımlı doğrulama kurulumu
    public bool $settingUpTwoFactor = false;

    public ?string $pendingSecret = null;

    public string $twoFactorCode = '';

    public array $recoveryCodes = [];

    public function mount(): void
    {
        $this->name = auth()->user()->name;
        $this->email = auth()->user()->email;
    }

    public function saveProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190|unique:users,email,'.$user->id,
        ]);

        $user->update(['name' => $this->name, 'email' => $this->email]);
        ActivityLogger::updated($user, 'Profil bilgileri güncellendi');

        $this->dispatch('notify', message: 'Bilgileriniz kaydedildi.');
    }

    public function changePassword(): void
    {
        $min = (int) config('cms.security.min_password_length', 10);
        $user = auth()->user();

        $this->validate([
            'currentPassword' => 'required',
            'newPassword' => ['required', "min:{$min}", 'regex:/[a-zA-Z]/', 'regex:/\d/', 'same:newPasswordConfirmation'],
        ], [
            'currentPassword.required' => 'Mevcut şifrenizi girin.',
            'newPassword.min' => "Yeni şifre en az {$min} karakter olmalı.",
            'newPassword.regex' => 'Yeni şifre hem harf hem rakam içermeli.',
            'newPassword.same' => 'Yeni şifre ile tekrarı aynı değil.',
        ]);

        if (! Hash::check($this->currentPassword, $user->password)) {
            throw ValidationException::withMessages(['currentPassword' => 'Mevcut şifreniz doğru değil.']);
        }

        $user->forceFill([
            'password' => Hash::make($this->newPassword),
            'password_changed_at' => now(),
        ])->save();

        ActivityLogger::log('password.changed', $user, 'Şifre değiştirildi');

        $this->reset('currentPassword', 'newPassword', 'newPasswordConfirmation');
        $this->dispatch('notify', message: 'Şifreniz güncellendi.');
    }

    public function startTwoFactor(TwoFactor $twoFactor): void
    {
        $this->pendingSecret = $twoFactor->generateSecret();
        $this->settingUpTwoFactor = true;
        $this->twoFactorCode = '';
    }

    public function confirmTwoFactor(TwoFactor $twoFactor): void
    {
        if (! $this->pendingSecret || ! $twoFactor->verifySecret($this->pendingSecret, $this->twoFactorCode)) {
            throw ValidationException::withMessages([
                'twoFactorCode' => 'Kod doğrulanamadı. Uygulamadaki güncel kodu girdiğinizden emin olun.',
            ]);
        }

        $codes = $twoFactor->generateRecoveryCodes();

        auth()->user()->forceFill([
            'two_factor_secret' => $this->pendingSecret,
            'two_factor_recovery_codes' => $codes,
            'two_factor_confirmed_at' => now(),
        ])->save();

        ActivityLogger::log('two_factor.enabled', auth()->user(), 'İki adımlı doğrulama açıldı');

        $this->recoveryCodes = $codes;
        $this->reset('settingUpTwoFactor', 'pendingSecret', 'twoFactorCode');
    }

    public function disableTwoFactor(): void
    {
        auth()->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        ActivityLogger::log('two_factor.disabled', auth()->user(), 'İki adımlı doğrulama kapatıldı');

        $this->reset('recoveryCodes');
        $this->dispatch('notify', message: 'İki adımlı doğrulama kapatıldı.');
    }

    public function render(TwoFactor $twoFactor)
    {
        return view('livewire.admin.account', [
            'user' => auth()->user(),
            'qrCode' => $this->pendingSecret
                ? $twoFactor->qrCodeSvg(auth()->user(), $this->pendingSecret)
                : null,
        ])->title('Hesabım');
    }
}
