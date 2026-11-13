<?php

declare(strict_types=1);

namespace App\Shared\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Gives a model a manual sort order stored in a `position` column.
 * New records are appended to the end of the list.
 *
 * @mixin Model
 */
trait HasPosition
{
    public static function bootHasPosition(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getAttribute('position') === null) {
                $model->setAttribute('position', (int) $model->newQuery()->max('position') + 1);
            }
        });
    }

    /**
     * @param  Builder<static>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('position')->orderBy($this->getKeyName());
    }
}
