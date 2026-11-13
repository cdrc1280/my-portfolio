<?php

declare(strict_types=1);

namespace App\Domains\Experience\Data;

use App\Shared\Support\Input;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Create/update input for a certificate, with an optional badge image. The issuer is optional.
 */
#[MapName(SnakeCaseMapper::class)]
final class CertificateData extends Data
{
    public function __construct(
        public string $name,
        #[WithCast(DateTimeInterfaceCast::class, format: '!Y-m')]
        public CarbonImmutable $issuedOn,
        public ?string $issuer = null,
        public ?string $credentialUrl = null,
        public ?UploadedFile $badge = null,
        public bool $removeBadge = false,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [
            ...$properties,
            'name' => trim((string) ($properties['name'] ?? '')),
            'issuer' => Input::nullableString($properties['issuer'] ?? null),
            'issued_on' => Input::nullableString($properties['issued_on'] ?? null),
            'credential_url' => Input::nullableString($properties['credential_url'] ?? null),
            'badge' => ($properties['badge'] ?? null) instanceof UploadedFile ? $properties['badge'] : null,
            'remove_badge' => Input::bool($properties['remove_badge'] ?? false),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'issuer' => ['nullable', 'string', 'max:150'],
            'issued_on' => ['required', 'date_format:Y-m'],
            'credential_url' => ['nullable', 'url:http,https', 'max:255'],
            'badge' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'remove_badge' => ['boolean'],
        ];
    }
}
