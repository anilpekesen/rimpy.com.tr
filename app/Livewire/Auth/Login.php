<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\LoginGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.guest')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'E-posta adresinizi girin.',
            'email.email' => 'Geçerli bir e-posta adresi girin.',
            'password.required' => 'Şifrenizi girin.',
        ]);

        $ip = request()->ip();

        if (LoginGuard::tooManyAttempts($this->email, $ip)) {
            $minutes = LoginGuard::minutesUntilUnlock($this->email, $ip);

            throw ValidationException::withMessages([
                'email' => "Çok fazla hatalı deneme yapıldı. {$minutes} dakika sonra tekrar deneyin.",
            ]);
        }

        $user = User::where('email', $this->email)->first();

        // Hesap yoksa da şifre karşılaştırması yapılır; böylece cevap süresinden
        // hangi e-postaların kayıtlı olduğu anlaşılamaz.
        $valid = $user
            ? Hash::check($this->password, $user->password)
            : Hash::check($this->password, '$2y$12$'.str_repeat('.', 53));

        if (! $valid || ! $user) {
            LoginGuard::recordFailure($this->email, $ip);
            $this->reset('password');

            $left = LoginGuard::remainingAttempts($this->email, $ip);

            throw ValidationException::withMessages([
                'email' => $left > 0 && $left <= 2
                    ? "E-posta veya şifre hatalı. {$left} deneme hakkınız kaldı."
                    : 'E-posta veya şifre hatalı.',
            ]);
        }

        if (! $user->is_active) {
            LoginGuard::recordFailure($this->email, $ip);

            throw ValidationException::withMessages([
                'email' => 'Bu hesap devre dışı bırakılmış. Site yöneticisiyle görüşün.',
            ]);
        }

        // İki adımlı doğrulama açıksa oturum burada açılmaz; kullanıcı önce
        // uygulamasındaki kodu girer.
        if ($user->hasTwoFactor()) {
            session()->put('2fa:user', ['id' => $user->id, 'remember' => $this->remember]);

            return $this->redirect(route('admin.two-factor'), navigate: true);
        }

        $this->completeLogin($user);

        return $this->redirectIntended(route('admin.dashboard'), navigate: true);
    }

    private function completeLogin(User $user): void
    {
        Auth::login($user, $this->remember);
        session()->regenerate();

        LoginGuard::recordSuccess($user, request()->ip());
        ActivityLogger::log('login', $user, "{$user->name} panele giriş yaptı");
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
