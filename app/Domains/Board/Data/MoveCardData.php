<?php

declare(strict_types=1);

namespace App\Domains\Board\Data;

use App\Domains\Board\Enums\CardStatus;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

/**
 * A drag on the board: the column the card lands in, and that column's card ids in their new order.
 */
final class MoveCardData extends Data
{
    /**
     * @param  list<int>  $ids
     */
    public function __construct(
        public CardStatus $status,
        public array $ids,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(CardStatus::class)],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ];
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        if (is_array($properties['ids'] ?? null)) {
            // Numeric strings become ints; anything else is left for the `integer` rule to reject.
            $properties['ids'] = array_map(fn (mixed $id): mixed => is_numeric($id) ? (int) $id : $id, $properties['ids']);
        }

        return $properties;
    }
}
