<?php

declare(strict_types=1);

namespace App\Domains\Contact\Data;

use App\Domains\Contact\Enums\BudgetRange;
use App\Domains\Contact\Enums\Currency;
use App\Domains\Contact\Enums\ProjectTimeline;
use App\Domains\Contact\Enums\ProjectType;
use App\Shared\Support\Input;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * A project brief from the public contact form. `website` is a honeypot: people never see it,
 * bots tend to fill it (see isSpam()).
 */
#[MapName(SnakeCaseMapper::class)]
final class ContactMessageData extends Data
{
    /** Name of the hidden field humans never see or fill in. */
    public const HONEYPOT = 'website';

    public function __construct(
        public string $name,
        public string $email,
        public string $message,
        public ?ProjectType $projectType = null,
        public ?BudgetRange $budget = null,
        public ?Currency $budgetCurrency = null,
        public ?ProjectTimeline $timeline = null,
        public ?string $website = null,
    ) {}

    public function isSpam(): bool
    {
        return filled($this->website);
    }

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        return [
            ...$properties,
            'name' => trim((string) ($properties['name'] ?? '')),
            'email' => Str::lower(trim((string) ($properties['email'] ?? ''))),
            'message' => trim((string) ($properties['message'] ?? '')),
            'project_type' => Input::nullableString($properties['project_type'] ?? null),
            'budget' => Input::nullableString($properties['budget'] ?? null),
            'budget_currency' => Input::nullableString($properties['budget_currency'] ?? null),
            'timeline' => Input::nullableString($properties['timeline'] ?? null),
            self::HONEYPOT => Input::nullableString($properties[self::HONEYPOT] ?? null),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'project_type' => ['required', Rule::enum(ProjectType::class)],
            'budget' => ['required', Rule::enum(BudgetRange::class)],
            'budget_currency' => ['required', Rule::enum(Currency::class)],
            'timeline' => ['required', Rule::enum(ProjectTimeline::class)],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            self::HONEYPOT => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return ['project_type' => 'project type', 'budget_currency' => 'currency'];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return [
            'project_type.required' => 'Pick the type of work so I can reply with the right details.',
            'budget.required' => 'Pick a budget range, or "Not sure yet".',
            'timeline.required' => 'Pick a timeline, or "Flexible".',
            'message.min' => 'Tell me a bit more: at least :min characters.',
        ];
    }
}
