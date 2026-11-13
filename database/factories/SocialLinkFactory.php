<?php

namespace Database\Factories;

use App\Domains\Profile\Enums\SocialPlatform;
use App\Domains\Profile\Models\SocialLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SocialLink>
 */
class SocialLinkFactory extends Factory
{
    protected $model = SocialLink::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform' => SocialPlatform::GitHub,
            'url' => 'https://github.com/'.fake()->userName(),
        ];
    }
}
