<?php

namespace Database\Factories;

use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(implode(' ', (array) fake()->unique()->words(3)));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(16),
            'problem' => fake()->paragraph(),
            'role' => fake()->randomElement(['Full-stack developer', 'Lead developer', 'Front-end developer']),
            'role_details' => fake()->paragraph(),
            'results' => fake()->paragraph(),
            'started_on' => fake()->dateTimeBetween('-4 years', '-1 year')->format('Y-m-01'),
            'ended_on' => null,
            'is_confidential' => false,
            'cover_image' => null,
            'live_url' => fake()->optional()->url(),
            'repo_url' => fake()->optional()->url(),
            'status' => ProjectStatus::Draft,
            'is_featured' => false,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => ProjectStatus::Published,
            'published_at' => now()->subDays(fake()->numberBetween(1, 365)),
        ]);
    }

    public function confidential(): static
    {
        return $this->state(fn () => ['is_confidential' => true, 'repo_url' => 'https://github.com/example/private']);
    }

    public function featured(): static
    {
        return $this->published()->state(fn () => ['is_featured' => true]);
    }
}
