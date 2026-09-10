<?php

namespace App\Livewire\Admin\Settings;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Users extends Component
{
    public bool $inviting = false;

    public string $name = '';

    public string $email = '';

    public string $role = 'editor';

    public ?string $generatedPassword = null;

    public function invite(): void
    {
        $this->authorizeOwnerOrAdmin();

        $this->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:190|unique:users,email',
            'role' => 'required|in:'.implode(',', array_keys(config('cms.roles'))),
        ], [
            'name.required' => 'Ad soyad girin.',
            'email.unique' => 'Bu e-posta ile bir kullanıcı zaten var.',
        ]);

        // Yönetici, kendisinden daha yetkili bir kullanıcı oluşturamaz.
        if (! auth()->user()->atLeast($this->role)) {
            $this->addError('role', 'Kendi yetkinizin üzerinde bir rol veremezsiniz.');

            return;
        }

        // Şifre burada üretilip bir kez gösterilir; e-posta ayarlanmamış
        // sitelerde de kullanıcı hesabına ulaşabilsin.
        $password = Str::password(14, symbols: false);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($password),
            'role' => $this->role,
            'is_active' => true,
        ]);

        ActivityLogger::created($user, "Kullanıcı eklendi: {$user->name} ({$user->roleLabel()})");

        $this->generatedPassword = $password;
        $this->reset('name', 'email', 'role', 'inviting');
    }

    public function toggleActive(int $id): void
    {
        $this->authorizeOwnerOrAdmin();
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: 'Kendi hesabınızı kapatamazsınız.');

            return;
        }

        if (! auth()->user()->atLeast($user->role)) {
            $this->dispatch('notify', message: 'Bu kullanıcıyı değiştirme yetkiniz yok.');

            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
        ActivityLogger::updated($user, ($user->is_active ? 'Hesap açıldı: ' : 'Hesap kapatıldı: ').$user->name);

        $this->dispatch('notify', message: $user->is_active ? 'Hesap etkin.' : 'Hesap devre dışı. Açık oturumları da sona erdi.');
    }

    public function changeRole(int $id, string $role): void
    {
        $this->authorizeOwnerOrAdmin();
        $user = User::findOrFail($id);

        if ($user->id === auth()->id() || ! auth()->user()->atLeast($user->role) || ! auth()->user()->atLeast($role)) {
            $this->dispatch('notify', message: 'Bu değişikliği yapma yetkiniz yok.');

            return;
        }

        // Sitede en az bir sahip kalmalı.
        if ($user->isOwner() && User::where('role', 'owner')->count() <= 1) {
            $this->dispatch('notify', message: 'Sitede en az bir site sahibi bulunmalıdır.');

            return;
        }

        $user->update(['role' => $role]);
        ActivityLogger::updated($user, "Rol değişti: {$user->name} → {$user->roleLabel()}");

        $this->dispatch('notify', message: 'Rol güncellendi.');
    }

    public function delete(int $id): void
    {
        $this->authorizeOwnerOrAdmin();
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', message: 'Kendi hesabınızı silemezsiniz.');

            return;
        }

        if ($user->isOwner() && User::where('role', 'owner')->count() <= 1) {
            $this->dispatch('notify', message: 'Sitede en az bir site sahibi bulunmalıdır.');

            return;
        }

        if (! auth()->user()->atLeast($user->role)) {
            $this->dispatch('notify', message: 'Bu kullanıcıyı silme yetkiniz yok.');

            return;
        }

        ActivityLogger::deleted($user, "Kullanıcı silindi: {$user->name}");
        $user->delete();

        $this->dispatch('notify', message: 'Kullanıcı silindi.');
    }

    private function authorizeOwnerOrAdmin(): void
    {
        abort_unless(auth()->user()->atLeast('admin'), 403);
    }

    public function render()
    {
        return view('livewire.admin.settings.users', [
            'users' => User::orderByDesc('role')->orderBy('name')->get(),
            'roles' => config('cms.roles'),
        ])->title('Kullanıcılar');
    }
}
