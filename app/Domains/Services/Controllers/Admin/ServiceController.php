<?php

declare(strict_types=1);

namespace App\Domains\Services\Controllers\Admin;

use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Profile\Actions\UpdateEngagementSettingsAction;
use App\Domains\Profile\Data\EngagementSettingsData;
use App\Domains\Profile\Models\Profile;
use App\Domains\Profile\Resources\EngagementSettingsResource;
use App\Domains\Services\Actions\CreateServiceAction;
use App\Domains\Services\Actions\DeleteServiceAction;
use App\Domains\Services\Actions\ReorderServicesAction;
use App\Domains\Services\Actions\UpdateServiceAction;
use App\Domains\Services\Data\ServiceData;
use App\Domains\Services\Models\Service;
use App\Domains\Services\Queries\ServiceQueries;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.Service::class, only: ['index']),
            new Middleware('can:create,'.Service::class, only: ['store']),
            new Middleware('can:update,service', only: ['update']),
            new Middleware('can:reorder,'.Service::class, only: ['reorder']),
            new Middleware('can:delete,service', only: ['destroy']),
            new Middleware('can:update,'.Profile::class, only: ['updateSettings']),
        ];
    }

    public function index(ServiceQueries $queries, GetProfileAction $getProfile): Response
    {
        return Inertia::render('admin/services/index', [
            'services' => $queries->ordered(),
            'engagement' => EngagementSettingsResource::fromModel($getProfile->execute()),
        ]);
    }

    public function store(ServiceData $data, CreateServiceAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Service added.');

        return to_route('admin.services.index');
    }

    public function update(ServiceData $data, Service $service, UpdateServiceAction $action): RedirectResponse
    {
        $action->execute($service, $data);

        $this->toast('Service updated.');

        return to_route('admin.services.index');
    }

    public function destroy(Service $service, DeleteServiceAction $action): RedirectResponse
    {
        $action->execute($service);

        $this->toast('Service deleted.');

        return to_route('admin.services.index');
    }

    public function reorder(ReorderData $data, ReorderServicesAction $action): RedirectResponse
    {
        $action->execute($data->ids);

        $this->toast('Order saved.');

        return to_route('admin.services.index');
    }

    public function updateSettings(EngagementSettingsData $data, UpdateEngagementSettingsAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Engagement settings saved.');

        return to_route('admin.services.index');
    }
}
