<?php

namespace Database\Factories;

use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Models\ProjectImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectImage>
 */
class ProjectImageFactory extends Factory
{
    protected $model = ProjectImage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'path' => 'projects/gallery/'.fake()->uuid().'.jpg',
            'alt' => fake()->sentence(4),
            'position' => fake()->numberBetween(1, 10),
        ];
    }
}
