<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tarayıcı tarafındaki koruma başlıkları. Bu başlıklar, bir açık ortaya
 * çıksa bile saldırganın onu kullanabileceği alanı daraltır: çerezin
 * çalınması, sayfanın görünmez bir çerçeveye gömülmesi ve MIME tipiyle
 * oynanması engellenir.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Permitted-Cross-Domain-Policies' => 'none',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), interest-cohort=()',
        ];

        if ($request->secure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value, false);
        }

        // Sunucu ve PHP sürümünü ifşa eden başlıkları kaldır.
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
