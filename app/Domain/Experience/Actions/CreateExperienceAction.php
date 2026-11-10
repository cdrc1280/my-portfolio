<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DataTransferObjects\ExperienceData;
use App\Domain\Experience\Models\Experience;

final class CreateExperienceAction
{
    public function execute(ExperienceData $data): Experience
    {
        return Experience::query()->create($data->toArray());
    }
}
