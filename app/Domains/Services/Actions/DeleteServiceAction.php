<?php

declare(strict_types=1);

namespace App\Domains\Services\Actions;

use App\Domains\Services\Models\Service;

final class DeleteServiceAction
{
    public function execute(Service $service): void
    {
        $service->delete();
    }
}
