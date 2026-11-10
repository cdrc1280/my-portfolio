import { Badge } from '@/components/ui/badge';
import type { ProjectStatus } from '@/features/projects/types';

export function ProjectStatusBadge({ status }: { status: ProjectStatus }) {
    return status === 'published' ? (
        <Badge variant="secondary">Published</Badge>
    ) : (
        <Badge variant="outline" className="text-muted-foreground">
            Draft
        </Badge>
    );
}
