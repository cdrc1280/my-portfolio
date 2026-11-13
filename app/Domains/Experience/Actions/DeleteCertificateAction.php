<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Models\Certificate;
use App\Shared\Actions\DeleteStoredFileAction;

final class DeleteCertificateAction
{
    public function __construct(
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Certificate $certificate): void
    {
        $path = $certificate->badge_path;
        $certificate->delete();
        $this->deleteFile->execute($path);
    }
}
