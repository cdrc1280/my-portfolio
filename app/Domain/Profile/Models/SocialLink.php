<?php

declare(strict_types=1);

namespace App\Domain\Profile\Models;

use App\Domain\Profile\Enums\SocialPlatform;
use App\Domain\Profile\Policies\ProfilePolicy;
use App\Domain\Shared\Concerns\HasPosition;
use Database\Factories\SocialLinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property SocialPlatform $platform
 * @property string $url
 * @property int $position
 */
#[Fillable(['platform', 'url', 'position'])]
#[UseFactory(SocialLinkFactory::class)]
#[UsePolicy(ProfilePolicy::class)]
class SocialLink extends Model
{
    /** @use HasFactory<SocialLinkFactory> */
    use HasFactory, HasPosition;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'platform' => SocialPlatform::class,
            'position' => 'integer',
        ];
    }
}
