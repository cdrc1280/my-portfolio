<?php

namespace Database\Factories;

use App\Domains\Contact\Enums\BudgetRange;
use App\Domains\Contact\Enums\Currency;
use App\Domains\Contact\Enums\ProjectTimeline;
use App\Domains\Contact\Enums\ProjectType;
use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactMessage>
 */
class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'project_type' => fake()->randomElement(ProjectType::cases()),
            'budget' => fake()->randomElement(BudgetRange::cases()),
            'budget_currency' => fake()->randomElement(Currency::cases()),
            'timeline' => fake()->randomElement(ProjectTimeline::cases()),
            'message' => fake()->paragraph(),
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'read_at' => null,
        ];
    }

    public function read(): static
    {
        return $this->state(fn () => ['read_at' => now()]);
    }
}
