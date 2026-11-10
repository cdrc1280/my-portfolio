<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\DataTransferObjects\CertificateData;
use App\Domain\Experience\Models\Certificate;

final class UpdateCertificateAction
{
    public function execute(Certificate $certificate, CertificateData $data): Certificate
    {
        $certificate->update($data->toArray());

        return $certificate;
    }
}
