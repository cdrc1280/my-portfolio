<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Domain\Blog\Models\Post;
use App\Domain\Experience\Models\Experience;
use App\Domain\GitHub\Actions\GetGitHubActivityAction;
use App\Domain\Projects\Models\Project;
use App\Domain\Skills\Models\SkillCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\PostCardResource;
use App\Http\Resources\ProjectCardResource;
use App\Http\Resources\SkillCategoryResource;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('public/home', [
            'featuredProjects' => ProjectCardResource::collection(
                Project::query()->published()->featured()->with('tags')->ordered()->limit(4)->get(),
            ),
            'skillCategories' => SkillCategoryResource::collection(SkillCategory::query()->with('skills')->ordered()->get()),
            'experiences' => ExperienceResource::collection(Experience::query()->ordered()->limit(4)->get()),
            'latestPosts' => PostCardResource::collection(
                Post::query()->published()->with('tags')->newestFirst()->limit(3)->get(),
            ),
            // Loaded after first paint so a slow GitHub API never blocks the page.
            'github' => Inertia::defer(fn (): ?array => app(GetGitHubActivityAction::class)->execute()?->toArray()),
        ]);
    }
}
