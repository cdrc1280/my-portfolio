<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DataTransferObjects\EducationData;
use App\Domain\Experience\Models\Education;

final class CreateEducationAction
{
    public function execute(EducationData $data): Education
    {
        return Education::query()->create($data->toArray());
    }
}
