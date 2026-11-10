<?php

namespace Database\Factories;

use App\Domain\Experience\Enums\EmploymentType;
use App\Domain\Experience\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    protected $model = Experience::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-8 years', '-1 year');

        return [
            'company' => fake()->company(),
            'company_url' => fake()->optional()->url(),
            'role' => fake()->jobTitle(),
            'location' => fake()->city(),
            'employment_type' => fake()->randomElement(EmploymentType::cases()),
            'started_on' => $start->format('Y-m-01'),
            'ended_on' => fake()->dateTimeBetween($start, 'now')->format('Y-m-01'),
            'summary' => fake()->sentence(14),
            'highlights' => fake()->sentences(2),
            'technologies' => ['Laravel', 'React'],
        ];
    }

    public function current(): static
    {
        return $this->state(fn () => ['ended_on' => null]);
    }
}
