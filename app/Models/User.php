<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'locale', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /** Rol seviyesi. Büyük olan küçüğü kapsar. */
    public function level(): int
    {
        return config("cms.roles.{$this->role}.level", 0);
    }

    public function roleLabel(): string
    {
        return config("cms.roles.{$this->role}.label", $this->role);
    }

    /** Bu kullanıcı en az verilen rol seviyesinde mi? */
    public function atLeast(string $role): bool
    {
        return $this->level() >= (int) config("cms.roles.{$role}.level", 999);
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /**
     * Model kısmi yüklenmiş olabilir (ör. `select()` ile çekilmiş bir kayıt);
     * ham öznitelikler üzerinden bakmak, eksik kolonda hata fırlatmak yerine
     * güvenli biçimde "kapalı" yanıtı verir.
     */
    public function hasTwoFactor(): bool
    {
        $attributes = $this->getAttributes();

        return filled($attributes['two_factor_secret'] ?? null)
            && filled($attributes['two_factor_confirmed_at'] ?? null);
    }

    /** Panelde kullanıcıyı temsil eden baş harfler. */
    public function initials(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
