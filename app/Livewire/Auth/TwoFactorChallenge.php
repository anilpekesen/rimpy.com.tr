<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\LoginGuard;
use App\Services\TwoFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class TwoFactorChallenge extends Component
{
    public string $code = '';

    public bool $useRecovery = false;

    public function mount()
    {
        // Bu ekrana yalnızca şifresini doğru girmiş biri gelebilir.
        if (! session()->has('2fa:user')) {
            return $this->redirect(route('admin.login'), navigate: true);
        }
    }

    public function verify(TwoFactor $twoFactor)
    {
        $pending = session('2fa:user');

        if (! $pending || ! $user = User::find($pending['id'])) {
            return $this->redirect(route('admin.login'), navigate: true);
        }

        $ip = request()->ip();

        if (LoginGuard::tooManyAttempts($user->email, $ip)) {
            session()->forget('2fa:user');

            throw ValidationException::withMessages([
                'code' => 'Çok fazla hatalı deneme yapıldı. Bir süre sonra tekrar deneyin.',
            ]);
        }

        $verified = $this->useRecovery
            ? $twoFactor->useRecoveryCode($user, $this->code)
            : $twoFactor->verify($user, $this->code);

        if (! $verified) {
            LoginGuard::recordFailure($user->email, $ip);
            $this->reset('code');

            throw ValidationException::withMessages([
                'code' => $this->useRecovery
                    ? 'Bu kurtarma kodu geçersiz ya da daha önce kullanılmış.'
                    : 'Kod doğrulanamadı. Uygulamadaki güncel kodu girin.',
            ]);
        }

        session()->forget('2fa:user');

        Auth::login($user, $pending['remember'] ?? false);
        session()->regenerate();

        LoginGuard::recordSuccess($user, $ip);
        ActivityLogger::log('login', $user, "{$user->name} iki adımlı doğrulamayla giriş yaptı");

        return $this->redirectIntended(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.two-factor');
    }
}
