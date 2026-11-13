import { Badge } from '@/components/ui/badge';
import type { AdminPost } from '@/types/domains/blog';

export function PostStatusBadge({ post }: { post: Pick<AdminPost, 'status' | 'is_scheduled'> }) {
    if (post.status === 'draft') {
        return (
            <Badge variant="outline" className="text-muted-foreground">
                Draft
            </Badge>
        );
    }

    return <Badge variant="secondary">{post.is_scheduled ? 'Scheduled' : 'Published'}</Badge>;
}
