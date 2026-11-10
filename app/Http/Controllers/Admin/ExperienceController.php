<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Experience\Actions\CreateExperienceAction;
use App\Domain\Experience\Actions\DeleteExperienceAction;
use App\Domain\Experience\Actions\ReorderCareerHistoryAction;
use App\Domain\Experience\Actions\UpdateExperienceAction;
use App\Domain\Experience\Enums\EmploymentType;
use App\Domain\Experience\Models\Certificate;
use App\Domain\Experience\Models\Education;
use App\Domain\Experience\Models\Experience;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Experience\StoreExperienceRequest;
use App\Http\Requests\Admin\Experience\UpdateExperienceRequest;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Resources\CertificateResource;
use App\Http\Resources\EducationResource;
use App\Http\Resources\ExperienceResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExperienceController extends Controller
{
    /**
     * One CMS page manages work experience, education and certificates.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', Experience::class);

        return Inertia::render('admin/experience/index', [
            'experiences' => ExperienceResource::collection(Experience::query()->ordered()->get()),
            'education' => EducationResource::collection(Education::query()->ordered()->get()),
            'certificates' => CertificateResource::collection(Certificate::query()->ordered()->get()),
            'employmentTypes' => EmploymentType::options(),
        ]);
    }

    public function store(StoreExperienceRequest $request, CreateExperienceAction $action): RedirectResponse
    {
        $action->execute($request->toData());

        $this->toast('Role added.');

        return to_route('admin.experience.index');
    }

    public function update(UpdateExperienceRequest $request, Experience $experience, UpdateExperienceAction $action): RedirectResponse
    {
        $action->execute($experience, $request->toData());

        $this->toast('Role updated.');

        return to_route('admin.experience.index');
    }

    public function destroy(Experience $experience, DeleteExperienceAction $action): RedirectResponse
    {
        $this->authorize('delete', $experience);

        $action->execute($experience);

        $this->toast('Role deleted.');

        return to_route('admin.experience.index');
    }

    public function reorder(ReorderRequest $request, ReorderCareerHistoryAction $action): RedirectResponse
    {
        $this->authorize('reorder', Experience::class);

        $action->execute(Experience::class, $request->ids());

        return to_route('admin.experience.index');
    }
}
