<?php

declare(strict_types=1);

namespace App\Domains\Services\Resources;

use App\Domains\Services\Models\Service;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A service on the public site and in the admin list.
 */
#[MapName(SnakeCaseMapper::class)]
final class ServiceResource extends Resource
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public ?string $icon,
    ) {}

    public static function fromModel(Service $service): self
    {
        return new self(
            id: $service->id,
            title: $service->title,
            description: $service->description,
            icon: $service->icon,
        );
    }
}
