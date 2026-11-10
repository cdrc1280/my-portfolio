<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DataTransferObjects\ExperienceData;
use App\Domain\Experience\Models\Experience;

final class UpdateExperienceAction
{
    public function execute(Experience $experience, ExperienceData $data): Experience
    {
        $experience->update($data->toArray());

        return $experience;
    }
}
