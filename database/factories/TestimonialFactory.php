<?php

namespace Database\Factories;

use App\Domains\Testimonials\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_name' => fake()->name(),
            'author_role' => fake()->jobTitle(),
            'company' => fake()->company(),
            'quote' => fake()->paragraph(),
            'has_permission' => false,
            'is_featured' => false,
        ];
    }

    public function publishable(): static
    {
        return $this->state(['has_permission' => true]);
    }

    public function featured(): static
    {
        return $this->state(['has_permission' => true, 'is_featured' => true]);
    }
}
