<?php

declare(strict_types=1);

namespace App\Domains\Projects\Controllers\Public;

use App\Domains\Projects\Queries\NextProjectQuery;
use App\Domains\Projects\Queries\ProjectQueries;
use App\Domains\Projects\Resources\ProjectResource;
use App\Domains\Testimonials\Queries\TestimonialQueries;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request, ProjectQueries $queries): Response
    {
        $tag = $request->string('tag')->trim()->toString() ?: null;

        return Inertia::render('public/projects/index', [
            'projects' => $queries->published($tag),
            'tags' => $queries->filterTags(),
            'activeTag' => $tag,
        ]);
    }

    public function show(string $slug, ProjectQueries $queries, TestimonialQueries $testimonials, NextProjectQuery $findNext): Response
    {
        $project = $queries->findPublished($slug);
        $next = $findNext->execute($project);

        return Inertia::render('public/projects/show', [
            'project' => ProjectResource::fromModel($project),
            'testimonial' => $testimonials->forProject($project),
            'next' => $next ? ['title' => $next->title, 'slug' => $next->slug, 'summary' => $next->summary] : null,
        ]);
    }
}
