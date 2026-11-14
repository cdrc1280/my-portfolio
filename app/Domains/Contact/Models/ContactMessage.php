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
 * @property string|null $project_type_other
 * @property BudgetRange|null $budget
 * @property Currency|null $budget_currency
 * @property string|null $budget_other
 * @property ProjectTimeline|null $timeline
 * @property string|null $timeline_other
 * @property string $message
 * @property string|null $ip_hash
 * @property CarbonImmutable|null $read_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 *
 * @method static ContactMessageQueryBuilder<static> query()
 */
#[Fillable([
    'name', 'email', 'project_type', 'project_type_other', 'budget', 'budget_currency', 'budget_other',
    'timeline', 'timeline_other', 'message', 'ip_hash', 'read_at',
])]
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
     * The project type, or the sender's own words when they picked "Something else".
     */
    public function projectTypeLabel(): ?string
    {
        return $this->project_type === ProjectType::Other && filled($this->project_type_other)
            ? $this->project_type_other
            : $this->project_type?->label();
    }

    /**
     * The budget as the client stated it, in their currency (briefs without one were in USD), or the
     * amount in their own words when they picked "Another amount".
     */
    public function budgetLabel(): ?string
    {
        if ($this->budget === BudgetRange::Other && filled($this->budget_other)) {
            return $this->budget_other;
        }

        return $this->budget?->labelIn($this->budget_currency ?? Currency::Usd);
    }

    /**
     * The timeline, or the sender's own words when they picked "Something else".
     */
    public function timelineLabel(): ?string
    {
        return $this->timeline === ProjectTimeline::Other && filled($this->timeline_other)
            ? $this->timeline_other
            : $this->timeline?->label();
    }
}
