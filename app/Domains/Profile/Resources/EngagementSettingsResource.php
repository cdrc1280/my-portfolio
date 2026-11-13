<?php

declare(strict_types=1);

namespace App\Domains\Profile\Resources;

use App\Domains\Profile\Models\Profile;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * The engagement settings (stored on the profile) for the admin Services page.
 */
#[MapName(SnakeCaseMapper::class)]
final class EngagementSettingsResource extends Resource
{
    /**
     * @param  list<array{title: string, description: string}>  $engagementTypes
     * @param  list<array{title: string, description: string}>  $processSteps
     */
    public function __construct(
        public ?string $startingRate,
        public array $engagementTypes,
        public array $processSteps,
    ) {}

    public static function fromModel(Profile $profile): self
    {
        return new self(
            startingRate: $profile->starting_rate,
            engagementTypes: $profile->engagement_types ?? [],
            processSteps: $profile->process_steps ?? [],
        );
    }
}
