import { TechBadgeList } from '@/components/custom/tech-badge';
import type { Tag } from '@/types';

/** Technology tags from the API as TechBadges. */
export function TagList({ tags, className }: { tags: Tag[]; className?: string }) {
    return <TechBadgeList items={tags.map((tag) => tag.name)} className={className} />;
}
