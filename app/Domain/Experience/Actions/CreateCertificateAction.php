<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DataTransferObjects\CertificateData;
use App\Domain\Experience\Models\Certificate;

final class CreateCertificateAction
{
    public function execute(CertificateData $data): Certificate
    {
        return Certificate::query()->create($data->toArray());
    }
}
