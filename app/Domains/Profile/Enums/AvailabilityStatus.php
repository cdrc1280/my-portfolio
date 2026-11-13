<?php

declare(strict_types=1);

namespace App\Domains\Profile\Enums;

use App\Shared\Concerns\HasOptions;

enum AvailabilityStatus: string
{
    use HasOptions;

    case Available = 'available';
    case Limited = 'limited';
    case Unavailable = 'unavailable';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available for new work',
            self::Limited => 'Limited availability',
            self::Unavailable => 'Not taking new work',
        };
    }
}
