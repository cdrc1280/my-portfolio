import { TrendingDown, TrendingUp } from 'lucide-react';
import { SegmentBar } from '@/components/custom/dashboard/segment-bar';
import type { LeadStats } from '@/types/domains/dashboard';

/** Share bars for a lead mix (budget or project type). */
function Mix({ title, rows }: { title: string; rows: LeadStats['budgets'] }) {
    const total = rows.reduce((sum, row) => sum + row.count, 0);

    if (total === 0) {
        return null;
    }

    return (
        <div className="space-y-2">
            <p className="text-xs text-muted-foreground">{title} · last 90 days</p>
            <ul className="space-y-1.5">
                {rows.map((row) => (
                    <li key={row.label} className="grid grid-cols-[1fr_auto] items-center gap-x-3 gap-y-1 text-xs">
                        <span className="truncate">{row.label}</span>
                        <span className="text-muted-foreground tabular-nums">{row.count}</span>
                        <SegmentBar
                            className="col-span-2"
                            filled={Math.round((row.count / total) * 10)}
                            segments={10}
                            label={`${row.label}: ${row.count} of ${total}`}
                        />
                    </li>
                ))}
            </ul>
        </div>
    );
}

export function LeadsSummary({ leads }: { leads: LeadStats }) {
    const delta = leads.this_week - leads.last_week;
    const Trend = delta >= 0 ? TrendingUp : TrendingDown;

    return (
        <div className="space-y-5">
            <dl className="grid grid-cols-2 gap-3">
                <div className="rounded-lg border p-3">
                    <dt className="text-xs text-muted-foreground">Unread briefs</dt>
                    <dd className="text-2xl font-semibold tabular-nums">{leads.unread}</dd>
                </div>
                <div className="rounded-lg border p-3">
                    <dt className="text-xs text-muted-foreground">This week</dt>
                    <dd className="flex items-baseline gap-2">
                        <span className="text-2xl font-semibold tabular-nums">{leads.this_week}</span>
                        <span className="inline-flex items-center gap-0.5 text-xs text-muted-foreground">
                            <Trend className="size-3.5" aria-hidden />
                            {leads.last_week} last week
                        </span>
                    </dd>
                </div>
            </dl>
            <Mix title="Budgets (USD equivalent)" rows={leads.budgets} />
            <Mix title="Project types" rows={leads.project_types} />
            {leads.budgets.length === 0 && leads.project_types.length === 0 && (
                <p className="text-sm text-muted-foreground">No briefs in the last 90 days yet.</p>
            )}
        </div>
    );
}
