import { router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { SocialIcon } from '@/components/shared/social-icon';
import { SortableList } from '@/components/shared/sortable-list';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { SocialLinkDialog } from '@/features/profile/components/social-link-dialog';
import type { SocialLink } from '@/features/profile/types';
import { useReorder } from '@/hooks/use-reorder';
import { destroy, reorder } from '@/routes/admin/social-links';
import type { SelectOption, SocialPlatform } from '@/types';

type SocialLinksEditorProps = {
    links: SocialLink[];
    platforms: SelectOption<SocialPlatform>[];
};

export function SocialLinksEditor({ links, platforms }: SocialLinksEditorProps) {
    const { items, reorder: persist, saving } = useReorder(links, reorder.url());

    return (
        <Card>
            <CardHeader className="flex-row items-start justify-between gap-4">
                <div className="space-y-1.5">
                    <CardTitle>Social links</CardTitle>
                    <CardDescription>Shown in the footer and on the contact page.</CardDescription>
                </div>
                <SocialLinkDialog
                    platforms={platforms}
                    trigger={
                        <Button size="sm" variant="outline">
                            <Plus aria-hidden /> Add link
                        </Button>
                    }
                />
            </CardHeader>
            <CardContent>
                {items.length === 0 ? (
                    <p className="text-sm text-muted-foreground">No links yet.</p>
                ) : (
                    <SortableList
                        items={items}
                        onReorder={persist}
                        disabled={saving}
                        itemLabel={(link) => link.label}
                        renderItem={(link) => (
                            <div className="flex items-center gap-3">
                                <SocialIcon
                                    platform={link.platform}
                                    className="size-4 shrink-0 text-muted-foreground"
                                />
                                <div className="min-w-0 flex-1">
                                    <p className="text-sm font-medium">{link.label}</p>
                                    <p className="truncate text-xs text-muted-foreground">{link.url}</p>
                                </div>
                                <SocialLinkDialog
                                    link={link}
                                    platforms={platforms}
                                    trigger={
                                        <Button variant="ghost" size="icon" aria-label={`Edit ${link.label}`}>
                                            <Pencil aria-hidden />
                                        </Button>
                                    }
                                />
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    aria-label={`Remove ${link.label}`}
                                    onClick={() => router.delete(destroy.url(link.id), { preserveScroll: true })}
                                >
                                    <Trash2 aria-hidden />
                                </Button>
                            </div>
                        )}
                    />
                )}
            </CardContent>
        </Card>
    );
}
