<?php

namespace Database\Factories;

use App\Domains\Experience\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Cloud Practitioner', 'Professional Scrum Master', 'Web Accessibility Specialist']),
            'issuer' => fake()->company(),
            'issued_on' => fake()->dateTimeBetween('-3 years')->format('Y-m-01'),
            'credential_url' => fake()->optional()->url(),
        ];
    }
}
