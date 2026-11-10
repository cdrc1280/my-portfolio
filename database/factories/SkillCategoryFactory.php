<?php

namespace Database\Factories;

use App\Domain\Skills\Models\SkillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SkillCategory>
 */
class SkillCategoryFactory extends Factory
{
    protected $model = SkillCategory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Backend', 'Frontend', 'Tooling', 'Databases', 'DevOps']).' '.fake()->unique()->numberBetween(1, 99999),
        ];
    }
}
