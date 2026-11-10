<?php

namespace Database\Factories;

use App\Domain\Skills\Models\Skill;
use App\Domain\Skills\Models\SkillCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Skill>
 */
class SkillFactory extends Factory
{
    protected $model = Skill::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'skill_category_id' => SkillCategory::factory(),
            'name' => fake()->unique()->randomElement(['Laravel', 'React', 'MySQL', 'Docker', 'Git', 'Redis']).' '.fake()->unique()->numberBetween(1, 99999),
        ];
    }
}
