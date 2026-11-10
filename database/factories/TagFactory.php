<?php

namespace Database\Factories;

use App\Domain\Tags\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Laravel', 'React', 'TypeScript', 'Vue.js', 'MySQL', 'Tailwind CSS', 'Inertia.js',
            'Filament', 'PostgreSQL', 'Redis', 'Docker', 'Livewire', 'Alpine.js', 'Pest', 'REST API',
        ]).' '.fake()->unique()->numberBetween(1, 99999);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
