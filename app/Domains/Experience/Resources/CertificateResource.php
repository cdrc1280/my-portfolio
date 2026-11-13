<?php

declare(strict_types=1);

namespace App\Domains\Experience\Resources;

use App\Domains\Experience\Models\Certificate;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A certificate or training (issued month as `YYYY-MM`).
 */
#[MapName(SnakeCaseMapper::class)]
final class CertificateResource extends Resource
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $issuer,
        public string $issuedOn,
        public ?string $credentialUrl,
        public ?string $badgeUrl,
    ) {}

    public static function fromModel(Certificate $certificate): self
    {
        return new self(
            id: $certificate->id,
            name: $certificate->name,
            issuer: $certificate->issuer,
            issuedOn: $certificate->issued_on->format('Y-m'),
            credentialUrl: $certificate->credential_url,
            badgeUrl: MediaUrl::for($certificate->badge_path),
        );
    }
}
