<?php

declare(strict_types=1);

namespace App\Shared\Concerns;

use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * Audit log defaults for every model: record which attributes changed (old → new) on create,
 * update and delete, skip saves that change nothing, and never store hidden or secret columns.
 */
trait LogsModelActivity
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept([...$this->getHidden(), 'ip_hash', 'updated_at', 'created_at'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
