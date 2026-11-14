<?php

declare(strict_types=1);

namespace App\Domains\Contact\Enums;

use App\Shared\Concerns\HasOptions;

enum ProjectType: string
{
    use HasOptions;

    case NewBuild = 'new_build';
    case FeatureWork = 'feature_work';
    case Maintenance = 'maintenance';
    case LegacyUpgrade = 'legacy_upgrade';
    case Consulting = 'consulting';
    /** The sender describes it in their own words (ContactMessage::project_type_other). */
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::NewBuild => 'New web application',
            self::FeatureWork => 'Feature work on an existing app',
            self::Maintenance => 'Maintenance & support',
            self::LegacyUpgrade => 'Legacy cleanup / upgrade',
            self::Consulting => 'Code review or consulting',
            self::Other => 'Something else',
        };
    }
}
