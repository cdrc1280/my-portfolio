<?php

declare(strict_types=1);

namespace App\Domains\Experience\Controllers\Admin;

use App\Domains\Experience\Actions\CreateExperienceAction;
use App\Domains\Experience\Actions\DeleteExperienceAction;
use App\Domains\Experience\Actions\ReorderCareerHistoryAction;
use App\Domains\Experience\Actions\UpdateExperienceAction;
use App\Domains\Experience\Data\ExperienceData;
use App\Domains\Experience\Enums\EmploymentType;
use App\Domains\Experience\Models\Experience;
use App\Domains\Experience\Queries\ExperienceQueries;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class ExperienceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.Experience::class, only: ['index']),
            new Middleware('can:create,'.Experience::class, only: ['store']),
            new Middleware('can:update,experience', only: ['update']),
            new Middleware('can:reorder,'.Experience::class, only: ['reorder']),
            new Middleware('can:delete,experience', only: ['destroy']),
        ];
    }

    public function index(ExperienceQueries $queries): Response
    {
        return Inertia::render('admin/experience/index', [
            'experiences' => $queries->rolesForAdmin(),
            'education' => $queries->education(),
            'certificates' => $queries->certificates(),
            'employmentTypes' => EmploymentType::options(),
        ]);
    }

    public function store(ExperienceData $data, CreateExperienceAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Role added.');

        return to_route('admin.experience.index');
    }

    public function update(ExperienceData $data, Experience $experience, UpdateExperienceAction $action): RedirectResponse
    {
        $action->execute($experience, $data);

        $this->toast('Role updated.');

        return to_route('admin.experience.index');
    }

    public function destroy(Experience $experience, DeleteExperienceAction $action): RedirectResponse
    {
        $action->execute($experience);

        $this->toast('Role deleted.');

        return to_route('admin.experience.index');
    }

    public function reorder(ReorderData $data, ReorderCareerHistoryAction $action): RedirectResponse
    {
        $action->execute(Experience::class, $data->ids);

        return to_route('admin.experience.index');
    }
}
