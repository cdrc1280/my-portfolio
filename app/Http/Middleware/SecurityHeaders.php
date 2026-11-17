<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Browser hardening headers for every web response. The CSP only sets directives that cannot break the
 * site (framing, <base>, form targets, plugins); script and style sources are left open because the
 * inline theme script, the Vite dev server and optional analytics would otherwise need nonces.
 * HSTS is set by the edge (Vercel, Caddy), not here.
 */
class SecurityHeaders
{
    private const HEADERS = [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()',
        'Content-Security-Policy' => "base-uri 'self'; form-action 'self'; frame-ancestors 'self'; object-src 'none'",
    ];

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach (self::HEADERS as $name => $value) {
            if (! $response->headers->has($name)) {
                $response->headers->set($name, $value);
            }
        }

        return $response;
    }
}
