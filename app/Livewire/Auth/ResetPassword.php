<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class ResetPassword extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = (string) request('email', '');
    }

    public function updatePassword()
    {
        $min = (int) config('cms.security.min_password_length', 10);

        $this->validate([
            'email' => 'required|email',
            'password' => ['required', 'confirmed', "min:{$min}", 'regex:/[a-zA-Z]/', 'regex:/\d/'],
        ], [
            'password.min' => "Şifre en az {$min} karakter olmalı.",
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
            'password.regex' => 'Şifre hem harf hem rakam içermeli.',
        ]);

        $status = Password::reset([
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'token' => $this->token,
        ], function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
                'password_changed_at' => now(),
            ])->save();

            ActivityLogger::log('password.reset', $user, "{$user->name} şifresini yeniledi");

            event(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages([
                'email' => 'Bu bağlantı geçersiz ya da süresi dolmuş. Yeniden istekte bulunun.',
            ]);
        }

        session()->flash('status', 'Şifreniz güncellendi. Yeni şifrenizle giriş yapabilirsiniz.');

        return $this->redirect(route('admin.login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
