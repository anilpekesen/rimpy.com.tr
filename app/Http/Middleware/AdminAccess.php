<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Panele girişin son kapısı. Oturum doğrulaması Laravel'in `auth` ara
 * katmanında yapılır; burada hesabın hâlâ geçerli olduğu ve isteğin izinli
 * bir yerden geldiği denetlenir.
 */
class AdminAccess
{
    public function handle(Request $request, Closure $next, ?string $minimumRole = null): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('admin.login');
        }

        // Hesap pasife alındıysa açık oturumlar da anında düşer.
        if (! $user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Hesabınız devre dışı bırakılmış.']);
        }

        // .env ile IP kısıtı tanımlandıysa panele yalnızca o adreslerden girilir.
        $allowed = config('cms.admin_ips');

        if ($allowed && ! $this->ipAllowed($request->ip(), $allowed)) {
            abort(403, 'Bu adresten panele erişilemez.');
        }

        if ($minimumRole && ! $user->atLeast($minimumRole)) {
            abort(403, 'Bu bölüm için yetkiniz yok.');
        }

        return $next($request);
    }

    /** Tam eşleşme ya da CIDR aralığı (192.168.1.0/24) destekler. */
    private function ipAllowed(?string $ip, array $allowed): bool
    {
        if ($ip === null) {
            return false;
        }

        foreach ($allowed as $rule) {
            if ($rule === $ip) {
                return true;
            }

            if (str_contains($rule, '/') && $this->inRange($ip, $rule)) {
                return true;
            }
        }

        return false;
    }

    private function inRange(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr, 2);

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false) {
            return false;
        }

        $mask = -1 << (32 - (int) $bits);

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
