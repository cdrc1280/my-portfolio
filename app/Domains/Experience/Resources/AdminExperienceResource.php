<?php

declare(strict_types=1);

namespace App\Domains\Experience\Resources;

use App\Domains\Experience\Models\Experience;

/**
 * Admin view of a work history entry: the public shape, but with the real company and URL.
 */
final class AdminExperienceResource
{
    public static function fromModel(Experience $experience): ExperienceResource
    {
        $resource = ExperienceResource::fromModel($experience);
        $resource->company = $experience->company;
        $resource->companyUrl = $experience->company_url;

        return $resource;
    }
}
