<?php

namespace Database\Factories;

use App\Domains\WorkApproach\Models\WorkApproach;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkApproach>
 */
class WorkApproachFactory extends Factory
{
    protected $model = WorkApproach::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(14),
            'snippet' => '$ make ci',
            'icon' => 'flask',
        ];
    }
}
