<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\User;

/**
 * Giriş denemelerini kayda alır ve kaba kuvvet saldırılarını durdurur.
 *
 * Kilitleme hem e-posta hem IP için ayrı ayrı işler: saldırgan tek hesabı
 * denerse o hesap, çok sayıda hesabı denerse o IP kilitlenir. Kilit süresi
 * dolduğunda sayaç kendiliğinden sıfırlanır.
 */
class LoginGuard
{
    public static function tooManyAttempts(string $email, string $ip): bool
    {
        return self::remainingAttempts($email, $ip) <= 0;
    }

    public static function remainingAttempts(string $email, string $ip): int
    {
        $max = (int) config('cms.security.max_login_attempts', 5);
        $since = now()->subMinutes((int) config('cms.security.lockout_minutes', 15));

        $failures = LoginAttempt::where('created_at', '>=', $since)
            ->where('successful', false)
            ->where(fn ($q) => $q->where('email', $email)->orWhere('ip_address', $ip))
            ->count();

        return max(0, $max - $failures);
    }

    /** Kilidin açılmasına kaç dakika kaldığı. */
    public static function minutesUntilUnlock(string $email, string $ip): int
    {
        $window = (int) config('cms.security.lockout_minutes', 15);

        $last = LoginAttempt::where('successful', false)
            ->where(fn ($q) => $q->where('email', $email)->orWhere('ip_address', $ip))
            ->latest('created_at')
            ->value('created_at');

        if (! $last) {
            return 0;
        }

        return max(1, $window - now()->diffInMinutes($last));
    }

    public static function recordFailure(string $email, string $ip): void
    {
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $ip,
            'successful' => false,
            'user_agent' => str(request()->userAgent())->limit(500)->toString(),
        ]);
    }

    public static function recordSuccess(User $user, string $ip): void
    {
        LoginAttempt::create([
            'email' => $user->email,
            'ip_address' => $ip,
            'successful' => true,
            'user_agent' => str(request()->userAgent())->limit(500)->toString(),
        ]);

        // Başarılı girişten sonra bu hesabın başarısız denemelerini temizle ki
        // sonraki gerçek hata sayacı sıfırdan başlasın.
        LoginAttempt::where('email', $user->email)->where('successful', false)->delete();

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ])->save();
    }

    /** Panelde uyarı göstermek için: son 24 saatteki başarısız deneme sayısı. */
    public static function recentFailures(int $hours = 24): int
    {
        return LoginAttempt::where('successful', false)
            ->where('created_at', '>=', now()->subHours($hours))
            ->count();
    }

    public static function prune(int $days = 30): int
    {
        return LoginAttempt::where('created_at', '<', now()->subDays($days))->delete();
    }
}
