<?php

declare(strict_types=1);

namespace App\Domains\Services\Models;

use App\Domains\Services\Policies\ServicePolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Something the owner offers clients, shown on Home and /services.
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string|null $icon
 * @property int $position
 */
#[Fillable(['title', 'description', 'icon', 'position'])]
#[UseFactory(ServiceFactory::class)]
#[UsePolicy(ServicePolicy::class)]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

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
