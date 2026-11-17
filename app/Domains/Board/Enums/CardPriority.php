<?php

declare(strict_types=1);

namespace App\Domains\Board\Enums;

use App\Shared\Concerns\HasOptions;

enum CardPriority: string
{
    use HasOptions;

    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Normal => 'Normal',
            self::High => 'High',
        };
    }
}
