<?php

declare(strict_types=1);

namespace App\Domains\Contact\Enums;

use App\Shared\Concerns\HasOptions;

/**
 * Budget brackets. The bracket is stored on its own and the amounts come from the brief's currency
 * (see Currency::thresholds()), so the same bracket reads "₱50,000 – ₱150,000" or "$1,000 – $3,000".
 * The case values are kept from when budgets were USD-only.
 */
enum BudgetRange: string
{
    use HasOptions;

    case Under1k = 'under_1k';
    case From1kTo3k = '1k_3k';
    case From3kTo8k = '3k_8k';
    case Over8k = '8k_plus';
    case NotSure = 'not_sure';

    /**
     * Label in USD, used where briefs in different currencies are compared (the dashboard mix).
     */
    public function label(): string
    {
        return $this->labelIn(Currency::Usd);
    }

    public function labelIn(Currency $currency): string
    {
        [$small, $medium, $large] = $currency->thresholds();

        return match ($this) {
            self::Under1k => 'Under '.$currency->format($small),
            self::From1kTo3k => $currency->format($small).' – '.$currency->format($medium),
            self::From3kTo8k => $currency->format($medium).' – '.$currency->format($large),
            self::Over8k => $currency->format($large).'+',
            self::NotSure => 'Not sure yet',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function optionsIn(Currency $currency): array
    {
        return array_map(
            fn (self $case): array => ['value' => $case->value, 'label' => $case->labelIn($currency)],
            self::cases(),
        );
    }
}
