<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Skills\Actions\CreateSkillCategoryAction;
use App\Domain\Skills\Actions\DeleteSkillCategoryAction;
use App\Domain\Skills\Actions\ReorderSkillCategoriesAction;
use App\Domain\Skills\Actions\UpdateSkillCategoryAction;
use App\Domain\Skills\Exceptions\SkillCategoryNotEmpty;
use App\Domain\Skills\Models\SkillCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Requests\Admin\Skills\StoreSkillCategoryRequest;
use App\Http\Requests\Admin\Skills\UpdateSkillCategoryRequest;
use Illuminate\Http\RedirectResponse;

class SkillCategoryController extends Controller
{
    public function store(StoreSkillCategoryRequest $request, CreateSkillCategoryAction $action): RedirectResponse
    {
        $category = $action->execute($request->toData());

        $this->toast("Category \"{$category->name}\" created.");

        return to_route('admin.skills.index');
    }

    public function update(UpdateSkillCategoryRequest $request, SkillCategory $skillCategory, UpdateSkillCategoryAction $action): RedirectResponse
    {
        $action->execute($skillCategory, $request->toData());

        $this->toast('Category renamed.');

        return to_route('admin.skills.index');
    }

    public function destroy(SkillCategory $skillCategory, DeleteSkillCategoryAction $action): RedirectResponse
    {
        $this->authorize('delete', $skillCategory);

        try {
            $action->execute($skillCategory);
            $this->toast('Category deleted.');
        } catch (SkillCategoryNotEmpty $exception) {
            $this->toast($exception->getMessage(), 'error');
        }

        return to_route('admin.skills.index');
    }

    public function reorder(ReorderRequest $request, ReorderSkillCategoriesAction $action): RedirectResponse
    {
        $this->authorize('reorder', SkillCategory::class);

        $action->execute($request->ids());

        return to_route('admin.skills.index');
    }
}
