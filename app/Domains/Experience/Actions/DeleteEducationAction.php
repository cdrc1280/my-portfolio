<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Models\Education;

final class DeleteEducationAction
{
    public function execute(Education $education): void
    {
        $education->delete();
    }
}
