<?php

namespace Database\Factories;

use App\Domains\Profile\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'headline' => 'Full-stack developer',
            'tagline' => fake()->sentence(12),
            'bio' => fake()->paragraphs(2, true),
            'location' => fake()->city(),
            'email' => fake()->safeEmail(),
            'phone' => null,
            'availability' => 'Open to opportunities',
        ];
    }
}
