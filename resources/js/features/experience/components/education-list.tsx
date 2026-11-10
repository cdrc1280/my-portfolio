import type { Education } from '@/features/experience/types';
import { formatPeriod } from '@/lib/format';

export function EducationList({ education }: { education: Education[] }) {
    return (
        <ul className="space-y-6">
            {education.map((entry) => (
                <li key={entry.id} className="flex flex-col gap-1 sm:flex-row sm:justify-between sm:gap-4">
                    <div>
                        <h3 className="font-medium">
                            {entry.qualification}
                            {entry.field_of_study && <span className="font-normal">, {entry.field_of_study}</span>}
                        </h3>
                        <p className="text-sm text-muted-foreground">{entry.institution}</p>
                        {entry.notes && <p className="mt-1 text-sm text-muted-foreground">{entry.notes}</p>}
                    </div>
                    <p className="shrink-0 text-sm text-muted-foreground tabular-nums">
                        {formatPeriod(entry.started_on, entry.ended_on)}
                    </p>
                </li>
            ))}
        </ul>
    );
}
