<?php

declare(strict_types=1);

namespace App\Domains\Board\Data;

use App\Domains\Board\Enums\CardPriority;
use App\Domains\Board\Enums\CardStatus;
use App\Shared\Support\Input;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Throwable;

/**
 * Create/update input for a board card. Status and priority default to "To do" and "Normal".
 */
#[MapName(SnakeCaseMapper::class)]
final class BoardCardData extends Data
{
    public function __construct(
        public string $title,
        public CardStatus $status,
        public CardPriority $priority,
        public ?string $notes = null,
        public ?CarbonImmutable $dueOn = null,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [
            ...$properties,
            'title' => trim((string) ($properties['title'] ?? '')),
            'notes' => Input::nullableString($properties['notes'] ?? null),
            'status' => Input::nullableString($properties['status'] ?? null) ?? CardStatus::Todo->value,
            'priority' => Input::nullableString($properties['priority'] ?? null) ?? CardPriority::Normal->value,
            'due_on' => self::date($properties['due_on'] ?? null),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:160'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::enum(CardStatus::class)],
            'priority' => ['required', Rule::enum(CardPriority::class)],
            'due_on' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return ['due_on' => 'due date'];
    }

    /**
     * A parsed date, null when empty, or the raw value so the `date` rule can reject it.
     */
    private static function date(mixed $value): mixed
    {
        if ($value instanceof CarbonImmutable || $value === null || $value === '') {
            return $value === '' ? null : $value;
        }

        try {
            return CarbonImmutable::parse((string) $value)->startOfDay();
        } catch (Throwable) {
            return $value;
        }
    }
}
