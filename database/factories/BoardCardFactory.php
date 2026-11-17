<?php

namespace Database\Factories;

use App\Domains\Board\Enums\CardPriority;
use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardCard>
 */
class BoardCardFactory extends Factory
{
    protected $model = BoardCard::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'status' => CardStatus::Todo,
            'priority' => CardPriority::Normal,
        ];
    }

    public function status(CardStatus $status): static
    {
        return $this->state(['status' => $status]);
    }
}
