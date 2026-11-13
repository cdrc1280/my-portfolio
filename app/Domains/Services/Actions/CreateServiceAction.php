<?php

declare(strict_types=1);

namespace App\Domains\Services\Actions;

use App\Domains\Services\Data\ServiceData;
use App\Domains\Services\Models\Service;
use Illuminate\Support\Facades\DB;

final class CreateServiceAction
{
    public function execute(ServiceData $data): Service
    {
        return DB::transaction(fn (): Service => Service::query()->create(self::columns($data)));
    }

    /**
     * @return array<string, mixed>
     */
    public static function columns(ServiceData $data): array
    {
        return [
            'title' => $data->title,
            'description' => $data->description,
            'icon' => $data->icon,
        ];
    }
}
