<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    public string $email = '';

    public bool $sent = false;

    public function send()
    {
        $this->validate(
            ['email' => 'required|email'],
            ['email.required' => 'E-posta adresinizi girin.', 'email.email' => 'Geçerli bir e-posta adresi girin.']
        );

        Password::sendResetLink(['email' => $this->email]);

        // Sonuç ne olursa olsun aynı mesaj gösterilir; böylece hangi
        // adreslerin kayıtlı olduğu dışarıdan öğrenilemez.
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
