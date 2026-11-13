<?php

declare(strict_types=1);

namespace App\Domains\Services\Actions;

use App\Domains\Services\Data\ServiceData;
use App\Domains\Services\Models\Service;
use Illuminate\Support\Facades\DB;

final class UpdateServiceAction
{
    public function execute(Service $service, ServiceData $data): Service
    {
        return DB::transaction(function () use ($service, $data): Service {
            $service->update(CreateServiceAction::columns($data));

            return $service;
        });
    }
}
