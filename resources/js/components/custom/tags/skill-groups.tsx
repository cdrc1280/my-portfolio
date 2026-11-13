import { Reveal } from '@/components/custom/reveal';
import { TechBadge } from '@/components/custom/tech-badge';
import type { SkillGroup } from '@/types/domains/tags';

/** Skills grouped by category as compact rows of tech badges. No levels or percentages. */
export function SkillGroups({ groups }: { groups: SkillGroup[] }) {
    return (
        <Reveal>
            <dl className="space-y-6">
                {groups.map((group) => (
                    <div key={group.category} className="space-y-2.5">
                        <dt className="font-mono text-xs text-foreground">{group.label.toLowerCase()}</dt>
                        <dd>
                            <ul className="flex flex-wrap gap-1.5" aria-label={group.label}>
                                {group.skills.map((skill) => (
                                    <li key={skill.id}>
                                        <TechBadge>{skill.name}</TechBadge>
                                    </li>
                                ))}
                            </ul>
                        </dd>
                    </div>
                ))}
            </dl>
        </Reveal>
    );
}
