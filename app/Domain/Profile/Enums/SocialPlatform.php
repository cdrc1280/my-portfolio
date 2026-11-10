<?php

declare(strict_types=1);

namespace App\Domain\Profile\Enums;

enum SocialPlatform: string
{
    case GitHub = 'github';
    case LinkedIn = 'linkedin';
    case X = 'x';
    case Email = 'email';
    case Website = 'website';
    case YouTube = 'youtube';
    case Dribbble = 'dribbble';

    public function label(): string
    {
        return match ($this) {
            self::GitHub => 'GitHub',
            self::LinkedIn => 'LinkedIn',
            self::X => 'X',
            self::Email => 'Email',
            self::Website => 'Website',
            self::YouTube => 'YouTube',
            self::Dribbble => 'Dribbble',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case): array => ['value' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }
}
