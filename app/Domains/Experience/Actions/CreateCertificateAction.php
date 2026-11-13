<?php

declare(strict_types=1);

namespace App\Domains\Experience\Actions;

use App\Domains\Experience\Data\CertificateData;
use App\Domains\Experience\Models\Certificate;
use App\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Support\Facades\DB;

final class CreateCertificateAction
{
    public function __construct(
        private readonly StoreUploadedFileAction $storeFile,
    ) {}

    public function execute(CertificateData $data): Certificate
    {
        return DB::transaction(fn (): Certificate => Certificate::query()->create([
            ...self::columns($data),
            'badge_path' => $data->badge !== null ? $this->storeFile->execute($data->badge, 'certificates') : null,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public static function columns(CertificateData $data): array
    {
        return [
            'name' => $data->name,
            'issuer' => $data->issuer,
            'issued_on' => $data->issuedOn,
            'credential_url' => $data->credentialUrl,
        ];
    }
}
