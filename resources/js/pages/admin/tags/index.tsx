import { Head } from '@inertiajs/react';
import { Lightbulb, Tags } from 'lucide-react';
import { AdminSplit, BreakdownList, countBy, SidePanel, StatGrid, TipList } from '@/components/custom/admin-split';
import { PageHeader } from '@/components/custom/page-header';
import { SortableCrudList } from '@/components/custom/sortable-crud-list';
import { Badge } from '@/components/ui/badge';
import { destroy, index, reorder } from '@/routes/admin/tags';
import type { SelectOption, TagCategory } from '@/types';
import type { AdminTag } from '@/types/domains/tags';
import { TagDialog } from './tag-dialog';

type Props = {
    tags: AdminTag[];
    categories: SelectOption<TagCategory>[];
};

export default function TagsIndex({ tags, categories }: Props) {
    const skills = tags.filter((tag) => tag.category !== null).length;

    const unused = tags.filter((tag) => tag.projects_count + tag.posts_count === 0).length;
    const mostUsed = [...tags]
        .map((tag) => ({ label: tag.name, count: tag.projects_count + tag.posts_count }))
        .filter((tag) => tag.count > 0)
        .sort((a, b) => b.count - a.count)
        .slice(0, 6);

    const aside = (
        <>
            <SidePanel title="Overview" icon={Tags}>
                <StatGrid
                    stats={[
                        { label: 'Tags', value: tags.length },
                        { label: 'Skills', value: skills, hint: 'Have a category' },
                        { label: 'Unused', value: unused, hint: 'No projects or posts' },
                        { label: 'Categories', value: categories.length },
                    ]}
                />
            </SidePanel>
            <SidePanel title="Skills by category">
                <BreakdownList
                    items={countBy(
                        tags.filter((tag) => tag.category_label),
                        (tag) => tag.category_label as string,
                    )}
                    empty="Give a tag a category to list it as a skill."
                />
            </SidePanel>
            <SidePanel title="Most used">
                <BreakdownList items={mostUsed} empty="No tag is attached to a project or post yet." />
            </SidePanel>
            <SidePanel title="Tips" icon={Lightbulb}>
                <TipList tips={['Unused tags without a category are safe to delete.']} />
            </SidePanel>
        </>
    );

    return (
        <>
            <Head title="Tags & skills" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Tags & skills"
                    description={`Technologies for project badges and filters. The ${skills} with a category appear in the skills section, in this order.`}
                />
                <AdminSplit aside={aside}>
                    <SortableCrudList
                        items={tags}
                        noun="tag"
                        reorderUrl={reorder.url()}
                        destroyUrl={(tag) => destroy.url(tag.id)}
                        itemLabel={(tag) => tag.name}
                        renderSummary={(tag) => (
                            <div className="flex flex-wrap items-center gap-2">
                                <span className="font-medium">{tag.name}</span>
                                {tag.category_label && <Badge variant="secondary">{tag.category_label}</Badge>}
                                <span className="text-xs text-muted-foreground">
                                    {tag.projects_count} projects · {tag.posts_count} posts
                                </span>
                            </div>
                        )}
                        renderDialog={(trigger, tag) => (
                            <TagDialog tag={tag} categories={categories} trigger={trigger} />
                        )}
                    />
                </AdminSplit>
            </div>
        </>
    );
}

TagsIndex.layout = {
    breadcrumbs: [{ title: 'Tags & skills', href: index() }],
};
