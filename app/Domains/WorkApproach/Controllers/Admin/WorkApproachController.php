<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Controllers\Admin;

use App\Domains\WorkApproach\Actions\CreateWorkApproachAction;
use App\Domains\WorkApproach\Actions\DeleteWorkApproachAction;
use App\Domains\WorkApproach\Actions\ReorderWorkApproachesAction;
use App\Domains\WorkApproach\Actions\UpdateWorkApproachAction;
use App\Domains\WorkApproach\Data\WorkApproachData;
use App\Domains\WorkApproach\Models\WorkApproach;
use App\Domains\WorkApproach\Queries\WorkApproachQueries;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class WorkApproachController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.WorkApproach::class, only: ['index']),
            new Middleware('can:create,'.WorkApproach::class, only: ['store']),
            new Middleware('can:update,work_approach', only: ['update']),
            new Middleware('can:reorder,'.WorkApproach::class, only: ['reorder']),
            new Middleware('can:delete,work_approach', only: ['destroy']),
        ];
    }

    public function index(WorkApproachQueries $queries): Response
    {
        return Inertia::render('admin/work-approaches/index', [
            'approaches' => $queries->ordered(),
        ]);
    }

    public function store(WorkApproachData $data, CreateWorkApproachAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Approach added.');

        return to_route('admin.work-approaches.index');
    }

    public function update(WorkApproachData $data, WorkApproach $workApproach, UpdateWorkApproachAction $action): RedirectResponse
    {
        $action->execute($workApproach, $data);

        $this->toast('Approach updated.');

        return to_route('admin.work-approaches.index');
    }

    public function destroy(WorkApproach $workApproach, DeleteWorkApproachAction $action): RedirectResponse
    {
        $action->execute($workApproach);

        $this->toast('Approach deleted.');

        return to_route('admin.work-approaches.index');
    }

    public function reorder(ReorderData $data, ReorderWorkApproachesAction $action): RedirectResponse
    {
        $action->execute($data->ids);

        $this->toast('Order saved.');

        return to_route('admin.work-approaches.index');
    }
}
