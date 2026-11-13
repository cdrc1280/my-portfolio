<?php

declare(strict_types=1);

namespace App\Shared\Data;

use Spatie\LaravelData\Data;

/**
 * Input for every drag-to-reorder endpoint: the record ids in their new order.
 */
final class ReorderData extends Data
{
    /**
     * @param  list<int>  $ids
     */
    public function __construct(
        public array $ids,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
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
