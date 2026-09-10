<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('Deneme12345'),
            'role' => 'editor',
            'locale' => 'tr',
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function owner(): static
    {
        return $this->state(fn () => ['role' => 'owner']);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    /** İki adımlı doğrulaması açık bir hesap. */
    public function withTwoFactor(): static
    {
        return $this->state(fn () => [
            'two_factor_secret' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567',
            'two_factor_recovery_codes' => ['aaaaa-bbbbb'],
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
