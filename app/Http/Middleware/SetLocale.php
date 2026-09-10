<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ön yüzün dilini adresteki ön ekten belirler (/tr/..., /en/...). Tek dilli
 * sitelerde ön ek hiç kullanılmaz ve varsayılan dil uygulanır.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        if (! $locale || ! Locales::exists($locale)) {
            $locale = Locales::default();
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
