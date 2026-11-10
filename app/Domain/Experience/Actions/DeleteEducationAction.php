<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Education;

final class DeleteEducationAction
{
    public function execute(Education $education): void
    {
        $education->delete();
    }
}
