import type { ProjectMetric } from '@/types/domains/projects';

/** Outcome metrics ("Load time · -60%") as a row of stat tiles. */
export function ProjectMetrics({ metrics }: { metrics: ProjectMetric[] }) {
    if (metrics.length === 0) {
        return null;
    }

    return (
        <dl className="grid grid-cols-2 gap-3 sm:grid-cols-3">
            {metrics.map((metric) => (
                <div key={metric.label} className="space-y-1 rounded-lg border border-border bg-card p-4">
                    <dt className="font-mono text-xs">{metric.label}</dt>
                    <dd className="text-2xl font-semibold tracking-tight text-foreground tabular-nums">
                        {metric.value}
                    </dd>
                </div>
            ))}
        </dl>
    );
}
