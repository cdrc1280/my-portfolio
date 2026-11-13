<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Actions;

use App\Domains\WorkApproach\Models\WorkApproach;

final class DeleteWorkApproachAction
{
    public function execute(WorkApproach $workApproach): void
    {
        $workApproach->delete();
    }
}
