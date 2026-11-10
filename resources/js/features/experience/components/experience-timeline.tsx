import type { Experience } from '@/features/experience/types';
import { formatPeriod } from '@/lib/format';

/** Vertical career timeline for the public site. */
export function ExperienceTimeline({ experiences }: { experiences: Experience[] }) {
    return (
        <ol className="relative space-y-10 border-l pl-6">
            {experiences.map((experience) => (
                <li key={experience.id} className="relative">
                    <span
                        className={
                            'absolute top-1.5 -left-[1.9rem] size-3 rounded-full border-2 border-background ' +
                            (experience.is_current ? 'bg-brand' : 'bg-muted-foreground/40')
                        }
                        aria-hidden
                    />
                    <div className="flex flex-col gap-1 sm:flex-row sm:items-baseline sm:justify-between sm:gap-4">
                        <h3 className="font-semibold tracking-tight">
                            {experience.role}{' '}
                            <span className="font-normal text-muted-foreground">
                                at{' '}
                                {experience.company_url ? (
                                    <a
                                        href={experience.company_url}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="hover:text-brand"
                                    >
                                        {experience.company}
                                    </a>
                                ) : (
                                    experience.company
                                )}
                            </span>
                        </h3>
                        <p className="shrink-0 text-sm text-muted-foreground tabular-nums">
                            <time dateTime={experience.started_on}>
                                {formatPeriod(experience.started_on, experience.ended_on)}
                            </time>
                        </p>
                    </div>
                    <p className="mt-0.5 text-sm text-muted-foreground">
                        {[experience.employment_type_label, experience.location].filter(Boolean).join(' · ')}
                    </p>
                    {experience.summary && <p className="mt-3 text-sm leading-relaxed">{experience.summary}</p>}
                    {experience.highlights.length > 0 && (
                        <ul className="mt-3 list-disc space-y-1.5 pl-5 text-sm text-muted-foreground marker:text-muted-foreground/50">
                            {experience.highlights.map((highlight) => (
                                <li key={highlight}>{highlight}</li>
                            ))}
                        </ul>
                    )}
                    {experience.technologies.length > 0 && (
                        <p className="mt-3 text-xs text-muted-foreground">{experience.technologies.join(' · ')}</p>
                    )}
                </li>
            ))}
        </ol>
    );
}
