<?php

declare(strict_types=1);

namespace App\Domains\Services\Controllers\Public;

use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Services\Queries\ServiceQueries;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ServicesController extends Controller
{
    public function __invoke(GetProfileAction $getProfile, ServiceQueries $services): Response
    {
        $profile = $getProfile->execute();

        return Inertia::render('public/services', [
            'services' => $services->ordered(),
            'engagementTypes' => $profile->engagement_types ?? [],
            'startingRate' => $profile->starting_rate,
            'processSteps' => $profile->process_steps ?? [],
        ]);
    }
}
