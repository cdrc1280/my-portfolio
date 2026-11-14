<?php

declare(strict_types=1);

namespace App\Domains\Contact\Enums;

use App\Shared\Concerns\HasOptions;

enum ProjectTimeline: string
{
    use HasOptions;

    case Asap = 'asap';
    case WithinMonth = 'within_month';
    case OneToThreeMonths = 'one_to_three_months';
    case Flexible = 'flexible';
    /** The sender describes it in their own words (ContactMessage::timeline_other). */
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Asap => 'As soon as possible',
            self::WithinMonth => 'Within a month',
            self::OneToThreeMonths => 'In 1–3 months',
            self::Flexible => 'Flexible',
            self::Other => 'Something else',
        };
    }
}
