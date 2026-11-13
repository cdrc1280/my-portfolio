import { formatYearRange } from '@/lib/format';
import type { Education } from '@/types/domains/experience';

export function EducationList({ education }: { education: Education[] }) {
    return (
        <ul className="space-y-6">
            {education.map((entry) => (
                <li key={entry.id} className="flex flex-col gap-1 sm:flex-row sm:justify-between sm:gap-6">
                    <div>
                        <h3 className="font-semibold text-foreground">
                            {entry.qualification}
                            {entry.field_of_study && <span className="font-normal">, {entry.field_of_study}</span>}
                        </h3>
                        <p className="text-sm">{entry.institution}</p>
                        {entry.notes && <p className="mt-1 text-sm">{entry.notes}</p>}
                    </div>
                    <p className="shrink-0 font-mono text-xs sm:pt-1">
                        {formatYearRange(entry.started_on, entry.ended_on)}
                    </p>
                </li>
            ))}
        </ul>
    );
}
