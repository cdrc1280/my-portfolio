<?php

declare(strict_types=1);

namespace App\Domains\Assistant\Data;

use Spatie\LaravelData\Data;

/**
 * A visitor's question to the "Ask about me" assistant.
 */
final class AssistantQuestionData extends Data
{
    public function __construct(
        public string $question,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return ['question' => ['required', 'string', 'max:300']];
    }
}
