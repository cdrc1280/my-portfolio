<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Queries;

use App\Domains\Contact\Enums\BudgetRange;
use App\Domains\Contact\Enums\ProjectType;
use App\Domains\Contact\Models\ContactMessage;

/**
 * Lead pipeline numbers for the dashboard: unread briefs, this week vs last week, and what the last
 * 90 days of briefs asked for (budget and project type).
 */
final class LeadStatsQuery
{
    private const MIX_DAYS = 90;

    /**
     * @return array{unread: int, this_week: int, last_week: int, budgets: list<array{label: string, count: int}>, project_types: list<array{label: string, count: int}>}
     */
    public function execute(): array
    {
        $weekStart = now()->startOfWeek();

        return [
            'unread' => ContactMessage::query()->unread()->count(),
            'this_week' => ContactMessage::query()->where('created_at', '>=', $weekStart)->count(),
            'last_week' => ContactMessage::query()
                ->whereBetween('created_at', [$weekStart->subWeek(), $weekStart])
                ->count(),
            'budgets' => $this->mix('budget', BudgetRange::cases()),
            'project_types' => $this->mix('project_type', ProjectType::cases()),
        ];
    }

    /**
     * Counts per enum case (in enum order), leaving out cases nobody picked.
     *
     * @param  array<int, BudgetRange|ProjectType>  $cases
     * @return list<array{label: string, count: int}>
     */
    private function mix(string $column, array $cases): array
    {
        $counts = ContactMessage::query()
            ->where('created_at', '>=', now()->subDays(self::MIX_DAYS))
            ->whereNotNull($column)
            ->toBase()
            ->select("{$column} as value")
            ->selectRaw('count(*) as aggregate')
            ->groupBy($column)
            ->pluck('aggregate', 'value');

        $mix = [];

        foreach ($cases as $case) {
            $count = (int) ($counts[$case->value] ?? 0);

            if ($count > 0) {
                $mix[] = ['label' => $case->label(), 'count' => $count];
            }
        }

        return $mix;
    }
}
