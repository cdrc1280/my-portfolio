<?php

declare(strict_types=1);

namespace App\Domain\Profile\Models;

use App\Domain\Profile\Policies\ProfilePolicy;
use Carbon\CarbonImmutable;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * The site owner's public identity. There is exactly one row; use
 * App\Domain\Profile\Actions\GetProfileAction to read it.
 *
 * @property int $id
 * @property string $name
 * @property string $headline
 * @property string|null $tagline
 * @property string|null $bio
 * @property string|null $location
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $availability
 * @property string|null $avatar_path
 * @property string|null $resume_path
 * @property CarbonImmutable|null $resume_updated_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable([
    'name', 'headline', 'tagline', 'bio', 'location', 'email', 'phone', 'availability',
    'avatar_path', 'resume_path', 'resume_updated_at',
])]
#[UseFactory(ProfileFactory::class)]
#[UsePolicy(ProfilePolicy::class)]
class Profile extends Model
{
    /** @use HasFactory<ProfileFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resume_updated_at' => 'datetime',
        ];
    }
}
