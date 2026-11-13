<?php

declare(strict_types=1);

namespace App\Domains\Profile\Data;

use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Shared\Support\Input;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * The dashboard's quick availability switch: status plus an optional note.
 */
#[MapName(SnakeCaseMapper::class)]
final class AvailabilityData extends Data
{
    public function __construct(
        public AvailabilityStatus $availabilityStatus,
        public ?string $availability = null,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [...$properties, 'availability' => Input::nullableString($properties['availability'] ?? null)];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'availability_status' => ['required', Rule::enum(AvailabilityStatus::class)],
            'availability' => ['nullable', 'string', 'max:150'],
        ];
    }
}
