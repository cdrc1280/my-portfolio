<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Resources;

use App\Domains\Testimonials\Models\Testimonial;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A testimonial in the admin list, with its publication flags.
 */
#[MapName(SnakeCaseMapper::class)]
final class AdminTestimonialResource extends Resource
{
    /**
     * @param  array{title: string, slug: string}|null  $project
     */
    public function __construct(
        public int $id,
        public string $authorName,
        public ?string $authorRole,
        public ?string $company,
        public ?string $photoUrl,
        public string $quote,
        public ?array $project,
        public ?int $projectId,
        public bool $hasPermission,
        public bool $isFeatured,
    ) {}

    public static function fromModel(Testimonial $testimonial): self
    {
        $public = TestimonialResource::fromModel($testimonial);

        return new self(
            id: $public->id,
            authorName: $public->authorName,
            authorRole: $public->authorRole,
            company: $public->company,
            photoUrl: $public->photoUrl,
            quote: $public->quote,
            project: $public->project,
            projectId: $testimonial->project_id,
            hasPermission: $testimonial->has_permission,
            isFeatured: $testimonial->is_featured,
        );
    }
}
