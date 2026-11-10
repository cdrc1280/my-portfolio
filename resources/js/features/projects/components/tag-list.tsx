import { Badge } from '@/components/ui/badge';
import type { Tag } from '@/types';

export function TagList({ tags, className }: { tags: Tag[]; className?: string }) {
    if (tags.length === 0) {
        return null;
    }

    return (
        <ul className={className ?? 'flex flex-wrap gap-1.5'} aria-label="Technologies">
            {tags.map((tag) => (
                <li key={tag.id}>
                    <Badge variant="secondary" className="font-normal">
                        {tag.name}
                    </Badge>
                </li>
            ))}
        </ul>
    );
}
