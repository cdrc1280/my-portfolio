import { Reveal } from '@/components/custom/reveal';
import { TechBadgeList } from '@/components/custom/tech-badge';
import { TimelineItem } from '@/components/custom/timeline-item';
import { formatPeriod } from '@/lib/format';
import type { Experience } from '@/types/domains/experience';

type ExperienceTimelineProps = {
    experiences: Experience[];
    /** Compact: at most `maxBullets` bullets and no tech badges (home page). */
    compact?: boolean;
    maxBullets?: number;
};

/** Career history as a vertical timeline. */
export function ExperienceTimeline({ experiences, compact = false, maxBullets = 3 }: ExperienceTimelineProps) {
    return (
        <Reveal>
            <ol>
                {experiences.map((experience) => (
                    <TimelineItem
                        key={experience.id}
                        role={experience.role}
                        company={experience.company ?? 'Confidential client'}
                        companyUrl={experience.company_url}
                        dates={formatPeriod(experience.started_on, experience.ended_on)}
                        dateTime={experience.started_on}
                        meta={
                            compact
                                ? null
                                : [
                                      experience.employment_type_label,
                                      experience.is_remote ? 'Remote' : experience.location,
                                  ]
                                      .filter(Boolean)
                                      .join(' · ')
                        }
                        bullets={compact ? experience.highlights.slice(0, maxBullets) : experience.highlights}
                        current={experience.is_current}
                    >
                        {!compact && experience.summary && (
                            <p className="mt-3 text-sm leading-relaxed">{experience.summary}</p>
                        )}
                        {!compact && <TechBadgeList items={experience.technologies} className="mt-4" />}
                    </TimelineItem>
                ))}
            </ol>
        </Reveal>
    );
}
