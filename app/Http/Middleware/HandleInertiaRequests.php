<?php

namespace App\Http\Middleware;

use App\Domains\Account\Resources\UserResource;
use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Services\Models\Service;
use App\Domains\Uses\Models\UsesItem;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'appUrl' => rtrim((string) config('app.url'), '/'),
            'site' => fn () => app(GetProfileAction::class)->snapshot()->toArray(),
            // Which optional public pages have content, so navigation never links to an empty page.
            'content' => fn () => [
                'services' => Service::query()->exists(),
                'uses' => UsesItem::query()->exists(),
            ],
            'auth' => [
                'user' => fn () => $request->user() ? UserResource::fromModel($request->user())->toArray() : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
