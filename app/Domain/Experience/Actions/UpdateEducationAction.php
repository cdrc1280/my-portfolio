<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DataTransferObjects\EducationData;
use App\Domain\Experience\Models\Education;

final class UpdateEducationAction
{
    public function execute(Education $education, EducationData $data): Education
    {
        $education->update($data->toArray());

        return $education;
    }
}
