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
 * A project brief from the public contact form. Picking "other" for the project type, budget or
 * timeline asks for the sender's own words in the matching `*_other` field; that text is dropped when
 * another option is picked. `website` is a honeypot: people never see it,
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
        public ?string $projectTypeOther = null,
        public ?BudgetRange $budget = null,
        public ?Currency $budgetCurrency = null,
        public ?string $budgetOther = null,
        public ?ProjectTimeline $timeline = null,
        public ?string $timelineOther = null,
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
        $projectType = Input::nullableString($properties['project_type'] ?? null);
        $budget = Input::nullableString($properties['budget'] ?? null);
        $timeline = Input::nullableString($properties['timeline'] ?? null);

        return [
            ...$properties,
            'name' => trim((string) ($properties['name'] ?? '')),
            'email' => Str::lower(trim((string) ($properties['email'] ?? ''))),
            'message' => trim((string) ($properties['message'] ?? '')),
            'project_type' => $projectType,
            'project_type_other' => self::otherAnswer($projectType, $properties['project_type_other'] ?? null),
            'budget' => $budget,
            'budget_currency' => Input::nullableString($properties['budget_currency'] ?? null),
            'budget_other' => self::otherAnswer($budget, $properties['budget_other'] ?? null),
            'timeline' => $timeline,
            'timeline_other' => self::otherAnswer($timeline, $properties['timeline_other'] ?? null),
            self::HONEYPOT => Input::nullableString($properties[self::HONEYPOT] ?? null),
        ];
    }

    /**
     * The sender's own words for an "other" choice (every such enum case is `other`), kept only while
     * "other" is picked.
     */
    private static function otherAnswer(?string $choice, mixed $answer): ?string
    {
        return $choice === 'other' ? Input::nullableString($answer) : null;
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
            'project_type_other' => ['nullable', 'required_if:project_type,'.ProjectType::Other->value, 'string', 'max:150'],
            'budget' => ['required', Rule::enum(BudgetRange::class)],
            'budget_currency' => ['required', Rule::enum(Currency::class)],
            'budget_other' => ['nullable', 'required_if:budget,'.BudgetRange::Other->value, 'string', 'max:150'],
            'timeline' => ['required', Rule::enum(ProjectTimeline::class)],
            'timeline_other' => ['nullable', 'required_if:timeline,'.ProjectTimeline::Other->value, 'string', 'max:150'],
            'message' => ['required', 'string', 'min:20', 'max:5000'],
            self::HONEYPOT => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function attributes(): array
    {
        return [
            'project_type' => 'project type',
            'project_type_other' => 'project type',
            'budget_currency' => 'currency',
            'budget_other' => 'budget',
            'timeline_other' => 'timeline',
        ];
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
            'project_type_other.required_if' => 'Describe the type of work in a few words.',
            'budget_other.required_if' => 'Write the budget you have in mind.',
            'timeline_other.required_if' => 'Write the timeline you have in mind.',
            'message.min' => 'Tell me a bit more: at least :min characters.',
        ];
    }
}
