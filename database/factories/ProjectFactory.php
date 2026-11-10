<?php

namespace Database\Factories;

use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
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
            'body' => fake()->paragraphs(3, true),
            'role' => fake()->randomElement(['Full-stack developer', 'Lead developer', 'Front-end developer']),
            'period' => (string) fake()->numberBetween(2021, 2026),
            'highlights' => fake()->sentences(3),
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

    public function featured(): static
    {
        return $this->published()->state(fn () => ['is_featured' => true]);
    }
}
