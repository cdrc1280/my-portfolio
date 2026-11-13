<?php

declare(strict_types=1);

namespace App\Domains\Projects\Controllers\Admin;

use App\Domains\Projects\Actions\CreateProjectAction;
use App\Domains\Projects\Actions\DeleteProjectAction;
use App\Domains\Projects\Actions\ReorderProjectsAction;
use App\Domains\Projects\Actions\UpdateProjectAction;
use App\Domains\Projects\Data\ProjectData;
use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Queries\ProjectQueries;
use App\Domains\Projects\Resources\AdminProjectResource;
use App\Domains\Tags\Queries\TagQueries;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.Project::class, only: ['index']),
            new Middleware('can:create,'.Project::class, only: ['create', 'store']),
            new Middleware('can:update,project', only: ['edit', 'update']),
            new Middleware('can:reorder,'.Project::class, only: ['reorder']),
            new Middleware('can:delete,project', only: ['destroy']),
        ];
    }

    public function index(ProjectQueries $queries): Response
    {
        return Inertia::render('admin/projects/index', [
            'projects' => $queries->forAdmin(),
        ]);
    }

    public function create(TagQueries $tags): Response
    {
        return Inertia::render('admin/projects/create', $this->formOptions($tags));
    }

    public function store(ProjectData $data, CreateProjectAction $action): RedirectResponse
    {
        $project = $action->execute($data);

        $this->toast("Project \"{$project->title}\" created.");

        return to_route('admin.projects.edit', $project);
    }

    public function edit(Project $project, TagQueries $tags): Response
    {
        return Inertia::render('admin/projects/edit', [
            'project' => AdminProjectResource::fromModel($project->load(['tags', 'images'])),
            ...$this->formOptions($tags),
        ]);
    }

    public function update(ProjectData $data, Project $project, UpdateProjectAction $action): RedirectResponse
    {
        $action->execute($project, $data);

        $this->toast('Project saved.');

        return to_route('admin.projects.edit', $project);
    }

    public function destroy(Project $project, DeleteProjectAction $action): RedirectResponse
    {
        $action->execute($project);

        $this->toast('Project deleted.');

        return to_route('admin.projects.index');
    }

    public function reorder(ReorderData $data, ReorderProjectsAction $action): RedirectResponse
    {
        $action->execute($data->ids);

        $this->toast('Order saved.');

        return to_route('admin.projects.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(TagQueries $tags): array
    {
        return [
            'tags' => $tags->allAsOptions(),
            'statuses' => ProjectStatus::options(),
        ];
    }
}
