<?php

declare(strict_types=1);

namespace App\Domains\Experience\Controllers\Admin;

use App\Domains\Experience\Actions\CreateEducationAction;
use App\Domains\Experience\Actions\DeleteEducationAction;
use App\Domains\Experience\Actions\ReorderCareerHistoryAction;
use App\Domains\Experience\Actions\UpdateEducationAction;
use App\Domains\Experience\Data\EducationData;
use App\Domains\Experience\Models\Education;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EducationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:create,'.Education::class, only: ['store']),
            new Middleware('can:update,education', only: ['update']),
            new Middleware('can:reorder,'.Education::class, only: ['reorder']),
            new Middleware('can:delete,education', only: ['destroy']),
        ];
    }

    public function store(EducationData $data, CreateEducationAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Education entry added.');

        return to_route('admin.experience.index');
    }

    public function update(EducationData $data, Education $education, UpdateEducationAction $action): RedirectResponse
    {
        $action->execute($education, $data);

        $this->toast('Education entry updated.');

        return to_route('admin.experience.index');
    }

    public function destroy(Education $education, DeleteEducationAction $action): RedirectResponse
    {
        $action->execute($education);

        $this->toast('Education entry deleted.');

        return to_route('admin.experience.index');
    }

    public function reorder(ReorderData $data, ReorderCareerHistoryAction $action): RedirectResponse
    {
        $action->execute(Education::class, $data->ids);

        return to_route('admin.experience.index');
    }
}
