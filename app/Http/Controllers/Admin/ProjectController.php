<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Projects\Actions\CreateProjectAction;
use App\Domain\Projects\Actions\DeleteProjectAction;
use App\Domain\Projects\Actions\ReorderProjectsAction;
use App\Domain\Projects\Actions\UpdateProjectAction;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Tags\Models\Tag;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Projects\StoreProjectRequest;
use App\Http\Requests\Admin\Projects\UpdateProjectRequest;
use App\Http\Requests\Admin\ReorderRequest;
use App\Http\Resources\Admin\ProjectResource;
use App\Http\Resources\TagResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Project::class);

        return Inertia::render('admin/projects/index', [
            'projects' => ProjectResource::collection(Project::query()->with('tags')->ordered()->get()),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Project::class);

        return Inertia::render('admin/projects/create', $this->formOptions());
    }

    public function store(StoreProjectRequest $request, CreateProjectAction $action): RedirectResponse
    {
        $project = $action->execute($request->toData());

        $this->toast("Project \"{$project->title}\" created.");

        return to_route('admin.projects.edit', $project);
    }

    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        return Inertia::render('admin/projects/edit', [
            'project' => ProjectResource::make($project->load(['tags', 'images'])),
            ...$this->formOptions(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project, UpdateProjectAction $action): RedirectResponse
    {
        $action->execute($project, $request->toData());

        $this->toast('Project saved.');

        return to_route('admin.projects.edit', $project);
    }

    public function destroy(Project $project, DeleteProjectAction $action): RedirectResponse
    {
        $this->authorize('delete', $project);

        $action->execute($project);

        $this->toast('Project deleted.');

        return to_route('admin.projects.index');
    }

    public function reorder(ReorderRequest $request, ReorderProjectsAction $action): RedirectResponse
    {
        $this->authorize('reorder', Project::class);

        $action->execute($request->ids());

        $this->toast('Order saved.');

        return to_route('admin.projects.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'tags' => TagResource::collection(Tag::query()->orderBy('name')->get()),
            'statuses' => ProjectStatus::options(),
        ];
    }
}
