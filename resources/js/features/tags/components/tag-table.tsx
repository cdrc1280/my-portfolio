import { router } from '@inertiajs/react';
import { Tags, Trash2 } from 'lucide-react';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { EmptyState } from '@/components/shared/empty-state';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { EditTagDialog } from '@/features/tags/components/edit-tag-dialog';
import type { AdminTag } from '@/features/tags/types';
import { destroy } from '@/routes/admin/tags';

export function TagTable({ tags }: { tags: AdminTag[] }) {
    if (tags.length === 0) {
        return (
            <EmptyState
                icon={Tags}
                title="No tags yet"
                description="Tags power the technology filter on the projects page."
            />
        );
    }

    return (
        <div className="rounded-xl border">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Name</TableHead>
                        <TableHead className="hidden sm:table-cell">Slug</TableHead>
                        <TableHead className="text-right">Projects</TableHead>
                        <TableHead className="w-24">
                            <span className="sr-only">Actions</span>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    {tags.map((tag) => (
                        <TableRow key={tag.id}>
                            <TableCell className="font-medium">{tag.name}</TableCell>
                            <TableCell className="hidden text-muted-foreground sm:table-cell">{tag.slug}</TableCell>
                            <TableCell className="text-right tabular-nums">{tag.projects_count}</TableCell>
                            <TableCell>
                                <div className="flex justify-end gap-1">
                                    <EditTagDialog tag={tag} />
                                    <ConfirmDialog
                                        title={`Delete "${tag.name}"?`}
                                        description={
                                            tag.projects_count > 0
                                                ? `It will be removed from ${tag.projects_count} project(s).`
                                                : 'This tag is not used anywhere.'
                                        }
                                        confirmLabel="Delete"
                                        onConfirm={() => router.delete(destroy.url(tag.id), { preserveScroll: true })}
                                        trigger={
                                            <Button variant="ghost" size="icon" aria-label={`Delete ${tag.name}`}>
                                                <Trash2 aria-hidden />
                                            </Button>
                                        }
                                    />
                                </div>
                            </TableCell>
                        </TableRow>
                    ))}
                </TableBody>
            </Table>
        </div>
    );
}
