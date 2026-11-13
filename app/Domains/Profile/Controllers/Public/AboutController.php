<?php

declare(strict_types=1);

namespace App\Domains\Profile\Controllers\Public;

use App\Domains\Experience\Queries\ExperienceQueries;
use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Profile\Resources\AboutProfileResource;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function __invoke(GetProfileAction $getProfile, ExperienceQueries $experience): Response
    {
        return Inertia::render('public/about', [
            'profile' => AboutProfileResource::fromModel($getProfile->execute()),
            'experiences' => $experience->roles(),
            'education' => $experience->education(),
            'certificates' => $experience->certificates(),
        ]);
    }
}
