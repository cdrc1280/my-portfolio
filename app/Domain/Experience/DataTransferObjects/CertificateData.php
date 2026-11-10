<?php

declare(strict_types=1);

namespace App\Domain\Experience\DataTransferObjects;

use App\Domain\Shared\Support\Input;
use Carbon\CarbonImmutable;

final readonly class CertificateData
{
    public function __construct(
        public string $name,
        public string $issuer,
        public CarbonImmutable $issuedOn,
        public ?string $credentialUrl = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            name: trim((string) $validated['name']),
            issuer: trim((string) $validated['issuer']),
            issuedOn: Input::month($validated['issued_on']) ?? CarbonImmutable::now()->startOfMonth(),
            credentialUrl: Input::nullableString($validated['credential_url'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'issuer' => $this->issuer,
            'issued_on' => $this->issuedOn,
            'credential_url' => $this->credentialUrl,
        ];
    }
}
