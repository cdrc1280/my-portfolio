<?php

namespace Database\Factories;

use App\Domain\Experience\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    protected $model = Education::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institution' => fake()->company().' University',
            'qualification' => 'Bachelor of Science',
            'field_of_study' => 'Computer Science',
            'started_on' => '2016-06-01',
            'ended_on' => '2020-04-01',
            'notes' => null,
        ];
    }
}
