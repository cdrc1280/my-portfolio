<?php

declare(strict_types=1);

namespace App\Shared\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Persists a drag-and-drop order for any model using HasPosition.
 * Domain-specific reorder actions delegate to this one.
 */
final class ReorderAction
{
    /**
     * @param  class-string<Model>  $modelClass
     * @param  list<int>  $orderedIds
     */
    public function execute(string $modelClass, array $orderedIds): void
    {
        $orderedIds = array_values(array_unique($orderedIds));

        $model = new $modelClass;
        $known = $model->newQuery()->whereKey($orderedIds)->count();

        if ($known !== count($orderedIds)) {
            throw new InvalidArgumentException('Cannot reorder: one or more records do not exist.');
        }

        DB::transaction(function () use ($model, $orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                $model->newQuery()->whereKey($id)->update(['position' => $index + 1]);
            }
        });
    }
}
