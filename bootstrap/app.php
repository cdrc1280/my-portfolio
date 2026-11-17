<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SecurityHeaders;
use App\Shared\Exceptions\DomainRuleViolation;
use App\Shared\Exceptions\FriendlyErrorResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withEvents(discover: [
        __DIR__.'/../app/Domains/*/Listeners',
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // In production, Caddy terminates HTTPS in front of nginx; trust its X-Forwarded-* headers so
        // URLs, redirects and secure cookies use https. Only Caddy can reach nginx (no published port).
        $middleware->trustProxies(at: '*');

        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            SecurityHeaders::class,
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // A blocked business rule (DomainRuleViolation) → error toast on the page the user came from.
        $exceptions->render(function (DomainRuleViolation $exception, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage()], 422);
            }

            Inertia::flash('toast', ['type' => 'error', 'message' => $exception->getMessage()]);

            return back();
        });

        // Every other error: a toast for form submits, the themed error page for page loads (see the class).
        $exceptions->respond(new FriendlyErrorResponse);
    })->create();
