import type { SkillCategory } from '@/features/skills/types';

/** Public, read-only skills overview grouped by category. */
export function SkillGroups({ categories }: { categories: SkillCategory[] }) {
    return (
        <dl className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            {categories.map((category) => (
                <div key={category.id} className="space-y-3">
                    <dt className="text-sm font-medium text-muted-foreground">{category.name}</dt>
                    <dd>
                        <ul className="flex flex-wrap gap-2">
                            {category.skills.map((skill) => (
                                <li key={skill.id} className="rounded-md border px-2.5 py-1 text-sm">
                                    {skill.name}
                                </li>
                            ))}
                        </ul>
                    </dd>
                </div>
            ))}
        </dl>
    );
}
