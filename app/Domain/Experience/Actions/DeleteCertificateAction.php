<?php

declare(strict_types=1);

namespace App\Domain\Experience\Actions;

use App\Domain\Experience\Models\Certificate;

final class DeleteCertificateAction
{
    public function execute(Certificate $certificate): void
    {
        $certificate->delete();
    }
}
