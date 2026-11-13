<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Data\ExperienceData;
use App\Domains\Experience\Models\Experience;
use Illuminate\Support\Facades\DB;

final class UpdateExperienceAction
{
    public function execute(Experience $experience, ExperienceData $data): Experience
    {
        DB::transaction(fn (): bool => $experience->update(CreateExperienceAction::columns($data)));

        return $experience;
    }
}
