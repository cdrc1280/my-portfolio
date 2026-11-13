<?php

declare(strict_types=1);

namespace App\Domains\Uses\Models;

use App\Domains\Uses\Policies\UsesItemPolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
use Database\Factories\UsesItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A tool or service on the /uses page, grouped by free-text category.
 *
 * @property int $id
 * @property string $category
 * @property string $name
 * @property string|null $description
 * @property string|null $url
 * @property int $position
 */
#[Fillable(['category', 'name', 'description', 'url', 'position'])]
#[UseFactory(UsesItemFactory::class)]
#[UsePolicy(UsesItemPolicy::class)]
class UsesItem extends Model
{
    /** @use HasFactory<UsesItemFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

    protected $table = 'uses_items';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
