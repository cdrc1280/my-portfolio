<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Data\EducationData;
use App\Domains\Experience\Models\Education;
use Illuminate\Support\Facades\DB;

final class UpdateEducationAction
{
    public function execute(Education $education, EducationData $data): Education
    {
        DB::transaction(fn (): bool => $education->update(CreateEducationAction::columns($data)));

        return $education;
    }
}
