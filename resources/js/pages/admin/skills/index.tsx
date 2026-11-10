import { Head } from '@inertiajs/react';
import { Plus, Sparkles } from 'lucide-react';
import { EmptyState } from '@/components/shared/empty-state';
import { PageHeader } from '@/components/shared/page-header';
import { SortableList } from '@/components/shared/sortable-list';
import { Button } from '@/components/ui/button';
import { CategoryDialog } from '@/features/skills/components/category-dialog';
import { CategoryPanel } from '@/features/skills/components/category-panel';
import type { SkillCategory } from '@/features/skills/types';
import { useReorder } from '@/hooks/use-reorder';
import { reorder } from '@/routes/admin/skill-categories';
import { index } from '@/routes/admin/skills';

type Props = {
    categories: SkillCategory[];
};

export default function SkillsIndex({ categories }: Props) {
    const { items, reorder: persist, saving } = useReorder(categories, reorder.url());

    const newCategory = (
        <CategoryDialog
            trigger={
                <Button>
                    <Plus aria-hidden /> New category
                </Button>
            }
        />
    );

    return (
        <>
            <Head title="Skills" />
            <div className="flex max-w-3xl flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Skills"
                    description="Group skills into categories. Drag categories and skills to reorder."
                    actions={newCategory}
                />
                {items.length > 0 ? (
                    <SortableList
                        items={items}
                        onReorder={persist}
                        disabled={saving}
                        itemLabel={(category) => category.name}
                        renderItem={(category) => <CategoryPanel category={category} categories={categories} />}
                    />
                ) : (
                    <EmptyState icon={Sparkles} title="No skill categories yet" action={newCategory} />
                )}
            </div>
        </>
    );
}

SkillsIndex.layout = {
    breadcrumbs: [{ title: 'Skills', href: index() }],
};
