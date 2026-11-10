<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Tags\Models\Tag;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectCardResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TagResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $tag = $request->string('tag')->trim()->toString() ?: null;

        return Inertia::render('public/projects/index', [
            'projects' => ProjectCardResource::collection(
                Project::query()->published()->withTag($tag)->with('tags')->ordered()->get(),
            ),
            // Only tags that would actually return something.
            'tags' => TagResource::collection(
                Tag::query()
                    ->whereHas('projects', fn (Builder $query) => $query->where('status', ProjectStatus::Published))
                    ->orderBy('name')
                    ->get(),
            ),
            'activeTag' => $tag,
        ]);
    }

    public function show(string $slug): Response
    {
        $project = Project::query()->published()->where('slug', $slug)->with(['tags', 'images'])->firstOrFail();

        $related = Project::query()
            ->published()
            ->whereKeyNot($project->id)
            ->whereHas('tags', fn (Builder $query) => $query->whereKey($project->tags->modelKeys()))
            ->with('tags')
            ->ordered()
            ->limit(3)
            ->get();

        return Inertia::render('public/projects/show', [
            'project' => ProjectResource::make($project),
            'related' => ProjectCardResource::collection($related),
        ]);
    }
}
