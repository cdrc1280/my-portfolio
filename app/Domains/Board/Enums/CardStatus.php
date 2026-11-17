<?php

declare(strict_types=1);

namespace App\Domains\Board\Enums;

use App\Shared\Concerns\HasOptions;

/**
 * The board's columns, left to right.
 */
enum CardStatus: string
{
    use HasOptions;

    case Ideas = 'ideas';
    case Todo = 'todo';
    case Doing = 'doing';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Ideas => 'Ideas',
            self::Todo => 'To do',
            self::Doing => 'Doing',
            self::Done => 'Done',
        };
    }
}
