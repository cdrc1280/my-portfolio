<?php

declare(strict_types=1);

namespace App\Domains\Board\Models;

use App\Domains\Board\Enums\CardPriority;
use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Policies\BoardCardPolicy;
use App\Shared\Concerns\HasPosition;
use App\Shared\Concerns\LogsModelActivity;
use Carbon\CarbonImmutable;
use Database\Factories\BoardCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A card on the admin's private board: a task, idea or note. Never shown on the public site.
 *
 * @property int $id
 * @property string $title
 * @property string|null $notes
 * @property CardStatus $status
 * @property CardPriority $priority
 * @property CarbonImmutable|null $due_on
 * @property int $position
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['title', 'notes', 'status', 'priority', 'due_on', 'position'])]
#[UseFactory(BoardCardFactory::class)]
#[UsePolicy(BoardCardPolicy::class)]
class BoardCard extends Model
{
    /** @use HasFactory<BoardCardFactory> */
    use HasFactory, HasPosition;

    use LogsModelActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CardStatus::class,
            'priority' => CardPriority::class,
            'due_on' => 'immutable_date',
            'position' => 'integer',
        ];
    }
}
