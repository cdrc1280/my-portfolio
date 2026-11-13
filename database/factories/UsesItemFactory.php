<?php

namespace Database\Factories;

use App\Domains\Uses\Models\UsesItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UsesItem>
 */
class UsesItemFactory extends Factory
{
    protected $model = UsesItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(['Editor', 'Terminal', 'Hardware', 'Services']),
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence(),
            'url' => fake()->url(),
        ];
    }
}
