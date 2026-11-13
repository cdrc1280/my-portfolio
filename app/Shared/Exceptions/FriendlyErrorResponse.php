<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * The fallback for every error a browser can hit, so nobody ever sees a blank or bare framework page:
 *
 * - a form submit or other non-GET Inertia request goes back to the page it came from with an error
 *   toast that says what happened (rate limit with the wait time, expired session, upload too large, …);
 * - a page load renders the themed `error` page for any 4xx/5xx status.
 *
 * JSON/API callers keep the plain response. In local and testing, 5xx keeps Laravel's debug page.
 */
final class FriendlyErrorResponse
{
    public function __invoke(Response $response, Throwable $exception, Request $request): Response
    {
        $status = $response->getStatusCode();

        if ($status < 400 || $request->expectsJson() || $request->is('api/*')) {
            return $response;
        }

        if ($status >= 500 && app()->environment(['local', 'testing'])) {
            return $response;
        }

        if ($request->header('X-Inertia') && ! $request->isMethod('GET') && $request->hasSession()) {
            Inertia::flash('toast', ['type' => 'error', 'message' => self::message($status, $exception)]);

            // 303 so the browser follows up with a GET, as Inertia expects after a non-GET request.
            return redirect()->back(303);
        }

        return Inertia::render('error', ['status' => $status])
            ->toResponse($request)
            ->setStatusCode($status);
    }

    /**
     * A short, human explanation for a toast.
     */
    public static function message(int $status, ?Throwable $exception = null): string
    {
        return match (true) {
            $status === 429 => self::tooManyAttempts($exception),
            $status === 419 => 'Your session expired. Please try again.',
            $status === 413 => 'That upload is too large. Please choose a smaller file.',
            $status === 403 => "You don't have permission to do that.",
            $status === 404 => 'That item no longer exists. Refresh the page and try again.',
            $status === 405 => "That action isn't available here.",
            $status >= 500 => 'Something went wrong on my side. Please try again in a moment.',
            default => 'Something went wrong. Please try again.',
        };
    }

    private static function tooManyAttempts(?Throwable $exception): string
    {
        $seconds = $exception instanceof ThrottleRequestsException
            ? (int) ($exception->getHeaders()['Retry-After'] ?? 0)
            : 0;

        if ($seconds <= 0) {
            return 'Too many attempts. Please wait a moment and try again.';
        }

        $minutes = (int) ceil($seconds / 60);

        if ($minutes >= 60) {
            $hours = (int) round($minutes / 60);

            return 'Too many attempts. Please try again in '.($hours === 1 ? 'an hour' : "{$hours} hours").'.';
        }

        return $minutes <= 1
            ? 'Too many attempts. Please wait a minute and try again.'
            : "Too many attempts. Please try again in {$minutes} minutes.";
    }
}
