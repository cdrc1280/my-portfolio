<?php

declare(strict_types=1);

namespace App\Domains\Site\Controllers\Public;

use App\Domains\Experience\Queries\ExperienceQueries;
use App\Domains\Projects\Queries\ProjectQueries;
use App\Domains\Services\Queries\ServiceQueries;
use App\Domains\Tags\Queries\SkillGroupsQuery;
use App\Domains\Testimonials\Queries\TestimonialQueries;
use App\Domains\WorkApproach\Queries\WorkApproachQueries;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(
        ProjectQueries $projects,
        WorkApproachQueries $approaches,
        ServiceQueries $services,
        SkillGroupsQuery $skillGroups,
        ExperienceQueries $experience,
        TestimonialQueries $testimonials,
    ): Response {
        return Inertia::render('public/home', [
            'featuredProjects' => $projects->featuredForHome(),
            'approaches' => $approaches->ordered(),
            'services' => $services->ordered(limit: 4),
            'skillGroups' => $skillGroups->execute(),
            'experiences' => $experience->roles(limit: 3),
            'testimonials' => $testimonials->featuredForHome(),
        ]);
    }
}
