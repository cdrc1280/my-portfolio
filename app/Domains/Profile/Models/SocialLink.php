<?php

declare(strict_types=1);

namespace App\Domains\Profile\Models;

use App\Domains\Profile\Enums\SocialPlatform;
use App\Domains\Profile\Policies\ProfilePolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
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

    use LogsModelActivity;

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
