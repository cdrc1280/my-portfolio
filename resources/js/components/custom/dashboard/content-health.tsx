import { Link } from '@inertiajs/react';
import { CheckCircle2, CircleAlert } from 'lucide-react';
import { SegmentBar } from '@/components/custom/dashboard/segment-bar';
import type { HealthCheck } from '@/types/domains/dashboard';

/** Checklist of what would make the portfolio more convincing; each item links to its fix. */
export function ContentHealth({ checks }: { checks: HealthCheck[] }) {
    const done = checks.filter((check) => check.ok).length;

    return (
        <div className="space-y-4">
            <div className="space-y-1.5">
                <p className="text-xs text-muted-foreground">
                    {done} of {checks.length} done
                </p>
                <SegmentBar filled={done} segments={checks.length} label={`${done} of ${checks.length} checks done`} />
            </div>
            <ul className="divide-y rounded-lg border">
                {checks.map((check) => (
                    <li key={check.key}>
                        <Link
                            href={check.href}
                            className="flex items-start gap-3 px-3 py-2.5 transition-colors hover:bg-muted/50"
                        >
                            {check.ok ? (
                                <CheckCircle2 className="mt-0.5 size-4 shrink-0 text-primary" aria-label="Done" />
                            ) : (
                                <CircleAlert className="mt-0.5 size-4 shrink-0 text-amber-500" aria-label="To do" />
                            )}
                            <span className="min-w-0">
                                <span className="block text-sm">{check.label}</span>
                                <span className="block truncate text-xs text-muted-foreground">{check.detail}</span>
                            </span>
                        </Link>
                    </li>
                ))}
            </ul>
        </div>
    );
}
