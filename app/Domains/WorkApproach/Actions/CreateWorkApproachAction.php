<?php

declare(strict_types=1);

namespace App\Domains\WorkApproach\Actions;

use App\Domains\WorkApproach\Data\WorkApproachData;
use App\Domains\WorkApproach\Models\WorkApproach;
use Illuminate\Support\Facades\DB;

final class CreateWorkApproachAction
{
    public function execute(WorkApproachData $data): WorkApproach
    {
        return DB::transaction(fn (): WorkApproach => WorkApproach::query()->create(self::columns($data)));
    }

    /**
     * @return array<string, mixed>
     */
    public static function columns(WorkApproachData $data): array
    {
        return [
            'title' => $data->title,
            'description' => $data->description,
            'snippet' => $data->snippet,
            'icon' => $data->icon,
        ];
    }
}
