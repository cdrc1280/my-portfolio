<?php

declare(strict_types=1);

namespace App\Domains\Profile\Models;

use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Domains\Profile\Policies\ProfilePolicy;
use App\Shared\Concerns\LogsModelActivity;
use Carbon\CarbonImmutable;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * The site owner's identity and site-wide settings. There is exactly one row; use
 * App\Domains\Profile\Actions\GetProfileAction to read it.
 *
 * Column meanings: `headline` = role title, `tagline` = value statement, `bio` = long bio (Markdown),
 * `availability` = availability note.
 *
 * @property int $id
 * @property string $name
 * @property string $headline
 * @property string|null $tagline
 * @property string|null $short_bio
 * @property string|null $bio
 * @property string|null $location
 * @property string|null $timezone
 * @property string|null $email
 * @property string|null $phone
 * @property AvailabilityStatus $availability_status
 * @property string|null $availability
 * @property string|null $working_hours
 * @property string|null $response_time
 * @property string|null $booking_url
 * @property string|null $currently_learning
 * @property string|null $starting_rate
 * @property list<array{title: string, description: string}>|null $engagement_types
 * @property list<array{title: string, description: string}>|null $process_steps
 * @property bool $blog_enabled
 * @property bool $assistant_enabled
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $og_image_path
 * @property string|null $avatar_path
 * @property string|null $resume_path
 * @property CarbonImmutable|null $resume_updated_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable([
    'name', 'headline', 'tagline', 'short_bio', 'bio', 'location', 'timezone', 'email', 'phone',
    'availability_status', 'availability', 'working_hours', 'response_time', 'booking_url',
    'currently_learning', 'starting_rate', 'engagement_types', 'process_steps', 'blog_enabled', 'assistant_enabled',
    'seo_title', 'seo_description', 'og_image_path', 'avatar_path', 'resume_path', 'resume_updated_at',
])]
#[UseFactory(ProfileFactory::class)]
#[UsePolicy(ProfilePolicy::class)]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    use LogsModelActivity;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'availability_status' => 'available',
        'blog_enabled' => true,
        'assistant_enabled' => true,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'availability_status' => AvailabilityStatus::class,
            'engagement_types' => 'array',
            'process_steps' => 'array',
            'blog_enabled' => 'boolean',
            'assistant_enabled' => 'boolean',
            'resume_updated_at' => 'datetime',
        ];
    }
}
