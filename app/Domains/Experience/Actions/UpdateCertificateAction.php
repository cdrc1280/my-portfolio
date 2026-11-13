<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Data\CertificateData;
use App\Domains\Experience\Models\Certificate;
use App\Shared\Actions\DeleteStoredFileAction;
use App\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Support\Facades\DB;

final class UpdateCertificateAction
{
    public function __construct(
        private readonly StoreUploadedFileAction $storeFile,
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Certificate $certificate, CertificateData $data): Certificate
    {
        $attributes = CreateCertificateAction::columns($data);
        $previous = $certificate->badge_path;

        if ($data->badge !== null) {
            $attributes['badge_path'] = $this->storeFile->execute($data->badge, 'certificates');
        } elseif ($data->removeBadge) {
            $attributes['badge_path'] = null;
        }

        DB::transaction(fn (): bool => $certificate->update($attributes));

        if (array_key_exists('badge_path', $attributes)) {
            $this->deleteFile->execute($previous);
        }

        return $certificate;
    }
}
