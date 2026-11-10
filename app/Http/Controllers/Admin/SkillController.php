<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Skills\Actions\CreateSkillAction;
use App\Domain\Skills\Actions\DeleteSkillAction;
use App\Domain\Skills\Actions\ReorderSkillsAction;
use App\Domain\Skills\Actions\UpdateSkillAction;
use App\Domain\Skills\Models\Skill;
use App\Domain\Skills\Models\SkillCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Requests\Admin\Skills\StoreSkillRequest;
use App\Http\Requests\Admin\Skills\UpdateSkillRequest;
use App\Http\Resources\SkillCategoryResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SkillController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Skill::class);

        return Inertia::render('admin/skills/index', [
            'categories' => SkillCategoryResource::collection(SkillCategory::query()->with('skills')->ordered()->get()),
        ]);
    }

    public function store(StoreSkillRequest $request, CreateSkillAction $action): RedirectResponse
    {
        $skill = $action->execute($request->toData());

        $this->toast("Added \"{$skill->name}\".");

        return to_route('admin.skills.index');
    }

    public function update(UpdateSkillRequest $request, Skill $skill, UpdateSkillAction $action): RedirectResponse
    {
        $action->execute($skill, $request->toData());

        $this->toast('Skill updated.');

        return to_route('admin.skills.index');
    }

    public function destroy(Skill $skill, DeleteSkillAction $action): RedirectResponse
    {
        $this->authorize('delete', $skill);

        $action->execute($skill);

        $this->toast('Skill removed.');

        return to_route('admin.skills.index');
    }

    public function reorder(ReorderRequest $request, ReorderSkillsAction $action): RedirectResponse
    {
        $this->authorize('reorder', Skill::class);

        $action->execute($request->ids());

        return to_route('admin.skills.index');
    }
}
