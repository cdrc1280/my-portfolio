<?php

declare(strict_types=1);

namespace App\Domains\Contact\Models;

use App\Domains\Contact\Enums\BudgetRange;
use App\Domains\Contact\Enums\Currency;
use App\Domains\Contact\Enums\ProjectTimeline;
use App\Domains\Contact\Enums\ProjectType;
use App\Domains\Contact\Policies\ContactMessagePolicy;
use App\Domains\Contact\QueryBuilders\ContactMessageQueryBuilder;
use App\Shared\Concerns\LogsModelActivity;
use Carbon\CarbonImmutable;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property ProjectType|null $project_type
 * @property BudgetRange|null $budget
 * @property Currency|null $budget_currency
 * @property ProjectTimeline|null $timeline
 * @property string $message
 * @property string|null $ip_hash
 * @property CarbonImmutable|null $read_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static ContactMessageQueryBuilder<static> query()
 */
#[Fillable(['name', 'email', 'project_type', 'budget', 'budget_currency', 'timeline', 'message', 'ip_hash', 'read_at'])]
#[UseEloquentBuilder(ContactMessageQueryBuilder::class)]
#[UseFactory(ContactMessageFactory::class)]
#[UsePolicy(ContactMessagePolicy::class)]
class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    use LogsModelActivity;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'project_type' => ProjectType::class,
            'budget' => BudgetRange::class,
            'budget_currency' => Currency::class,
            'timeline' => ProjectTimeline::class,
            'read_at' => 'datetime',
        ];
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * The budget as the client stated it, in their currency (briefs without one were in USD).
     */
    public function budgetLabel(): ?string
    {
        return $this->budget?->labelIn($this->budget_currency ?? Currency::Usd);
    }
}
