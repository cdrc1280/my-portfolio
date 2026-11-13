<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Models\Experience;

final class DeleteExperienceAction
{
    public function execute(Experience $experience): void
    {
        $experience->delete();
    }
}
