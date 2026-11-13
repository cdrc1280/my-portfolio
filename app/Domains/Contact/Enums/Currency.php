<?php

declare(strict_types=1);

namespace App\Domains\Contact\Enums;

use App\Shared\Concerns\HasOptions;

/**
 * Currencies a client can state their budget in. Each has its own rounded bracket thresholds,
 * roughly equivalent across currencies, so a brief always reads in the client's own money.
 */
enum Currency: string
{
    use HasOptions;

    case Php = 'PHP';
    case Usd = 'USD';
    case Eur = 'EUR';
    case Gbp = 'GBP';
    case Aud = 'AUD';
    case Cad = 'CAD';
    case Sgd = 'SGD';

    public function label(): string
    {
        return "{$this->value} ({$this->symbol()})";
    }

    public function symbol(): string
    {
        return match ($this) {
            self::Php => '₱',
            self::Usd => '$',
            self::Eur => '€',
            self::Gbp => '£',
            self::Aud => 'A$',
            self::Cad => 'C$',
            self::Sgd => 'S$',
        };
    }

    /**
     * Upper bounds of the small, medium and large budget brackets.
     *
     * @return array{int, int, int}
     */
    public function thresholds(): array
    {
        return match ($this) {
            self::Php => [50_000, 150_000, 450_000],
            self::Usd => [1_000, 3_000, 8_000],
            self::Eur => [1_000, 3_000, 7_500],
            self::Gbp => [800, 2_500, 6_500],
            self::Aud => [1_500, 4_500, 12_000],
            self::Cad => [1_400, 4_000, 11_000],
            self::Sgd => [1_300, 4_000, 10_500],
        };
    }

    public function format(int $amount): string
    {
        return $this->symbol().number_format($amount);
    }
}
