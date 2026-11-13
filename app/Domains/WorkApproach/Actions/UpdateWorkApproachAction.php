<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Actions;

use App\Domains\WorkApproach\Data\WorkApproachData;
use App\Domains\WorkApproach\Models\WorkApproach;
use Illuminate\Support\Facades\DB;

final class UpdateWorkApproachAction
{
    public function execute(WorkApproach $workApproach, WorkApproachData $data): WorkApproach
    {
        return DB::transaction(function () use ($workApproach, $data): WorkApproach {
            $workApproach->update(CreateWorkApproachAction::columns($data));

            return $workApproach;
        });
    }
}
