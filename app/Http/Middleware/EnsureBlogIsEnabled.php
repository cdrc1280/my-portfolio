<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domains\Profile\Actions\GetProfileAction;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The blog can be switched off in Settings; its public routes then 404.
 */
class EnsureBlogIsEnabled
{
    public function __construct(
        private readonly GetProfileAction $getProfile,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($this->getProfile->snapshot()->blogEnabled, 404);

        return $next($request);
    }
}
