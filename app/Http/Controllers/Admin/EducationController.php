<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Experience\Actions\CreateEducationAction;
use App\Domain\Experience\Actions\DeleteEducationAction;
use App\Domain\Experience\Actions\ReorderCareerHistoryAction;
use App\Domain\Experience\Actions\UpdateEducationAction;
use App\Domain\Experience\Models\Education;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Experience\StoreEducationRequest;
use App\Http\Requests\Admin\Experience\UpdateEducationRequest;
use App\Http\Requests\Admin\ReorderRequest;
use Illuminate\Http\RedirectResponse;

class EducationController extends Controller
{
    public function store(StoreEducationRequest $request, CreateEducationAction $action): RedirectResponse
    {
        $action->execute($request->toData());

        $this->toast('Education entry added.');

        return to_route('admin.experience.index');
    }

    public function update(UpdateEducationRequest $request, Education $education, UpdateEducationAction $action): RedirectResponse
    {
        $action->execute($education, $request->toData());

        $this->toast('Education entry updated.');

        return to_route('admin.experience.index');
    }

    public function destroy(Education $education, DeleteEducationAction $action): RedirectResponse
    {
        $this->authorize('delete', $education);

        $action->execute($education);

        $this->toast('Education entry deleted.');

        return to_route('admin.experience.index');
    }

    public function reorder(ReorderRequest $request, ReorderCareerHistoryAction $action): RedirectResponse
    {
        $this->authorize('reorder', Education::class);

        $action->execute(Education::class, $request->ids());

        return to_route('admin.experience.index');
    }
}
