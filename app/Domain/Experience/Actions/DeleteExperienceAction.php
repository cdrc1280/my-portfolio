<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Experience;

final class DeleteExperienceAction
{
    public function execute(Experience $experience): void
    {
        $experience->delete();
    }
}
