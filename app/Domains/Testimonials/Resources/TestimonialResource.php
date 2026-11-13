<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Resources;

use App\Domains\Testimonials\Models\Testimonial;
use App\Shared\Support\MediaUrl;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * A testimonial as shown publicly. `project` is set only when the relation was eager-loaded.
 */
#[MapName(SnakeCaseMapper::class)]
final class TestimonialResource extends Resource
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
    ) {}

    public static function fromModel(Testimonial $testimonial): self
    {
        $project = $testimonial->relationLoaded('project') ? $testimonial->project : null;

        return new self(
            id: $testimonial->id,
            authorName: $testimonial->author_name,
            authorRole: $testimonial->author_role,
            company: $testimonial->company,
            photoUrl: MediaUrl::for($testimonial->photo_path),
            quote: $testimonial->quote,
            project: $project ? ['title' => $project->title, 'slug' => $project->slug] : null,
        );
    }
}
