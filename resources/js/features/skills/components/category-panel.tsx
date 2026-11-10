import { router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { ConfirmDialog } from '@/components/shared/confirm-dialog';
import { SortableList } from '@/components/shared/sortable-list';
import { Button } from '@/components/ui/button';
import { CategoryDialog } from '@/features/skills/components/category-dialog';
import { SkillDialog } from '@/features/skills/components/skill-dialog';
import type { SkillCategory } from '@/features/skills/types';
import { useReorder } from '@/hooks/use-reorder';
import { destroy as destroyCategory } from '@/routes/admin/skill-categories';
import { destroy as destroySkill, reorder } from '@/routes/admin/skills';

type CategoryPanelProps = {
    category: SkillCategory;
    categories: SkillCategory[];
};

/** One category: its header actions and a sortable list of its skills. */
export function CategoryPanel({ category, categories }: CategoryPanelProps) {
    const { items, reorder: persist, saving } = useReorder(category.skills, reorder.url());

    return (
        <section aria-labelledby={`category-${category.id}`} className="space-y-3 py-1">
            <div className="flex items-center justify-between gap-2">
                <h2 id={`category-${category.id}`} className="font-medium">
                    {category.name}{' '}
                    <span className="text-sm font-normal text-muted-foreground">({category.skills.length})</span>
                </h2>
                <div className="flex gap-1">
                    <SkillDialog
                        categories={categories}
                        categoryId={category.id}
                        trigger={
                            <Button variant="ghost" size="sm">
                                <Plus aria-hidden /> Skill
                            </Button>
                        }
                    />
                    <CategoryDialog
                        category={category}
                        trigger={
                            <Button variant="ghost" size="icon" aria-label={`Rename ${category.name}`}>
                                <Pencil aria-hidden />
                            </Button>
                        }
                    />
                    <ConfirmDialog
                        title={`Delete "${category.name}"?`}
                        description="Only empty categories can be deleted."
                        confirmLabel="Delete"
                        onConfirm={() => router.delete(destroyCategory.url(category.id), { preserveScroll: true })}
                        trigger={
                            <Button variant="ghost" size="icon" aria-label={`Delete ${category.name}`}>
                                <Trash2 aria-hidden />
                            </Button>
                        }
                    />
                </div>
            </div>

            {items.length > 0 ? (
                <SortableList
                    items={items}
                    onReorder={persist}
                    disabled={saving}
                    itemLabel={(skill) => skill.name}
                    renderItem={(skill) => (
                        <div className="flex items-center justify-between gap-2">
                            <span className="text-sm">{skill.name}</span>
                            <div className="flex gap-1">
                                <SkillDialog
                                    categories={categories}
                                    categoryId={category.id}
                                    skill={skill}
                                    trigger={
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            className="size-8"
                                            aria-label={`Edit ${skill.name}`}
                                        >
                                            <Pencil aria-hidden />
                                        </Button>
                                    }
                                />
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    className="size-8"
                                    aria-label={`Remove ${skill.name}`}
                                    onClick={() => router.delete(destroySkill.url(skill.id), { preserveScroll: true })}
                                >
                                    <Trash2 aria-hidden />
                                </Button>
                            </div>
                        </div>
                    )}
                />
            ) : (
                <p className="rounded-lg border border-dashed px-4 py-3 text-sm text-muted-foreground">
                    No skills yet.
                </p>
            )}
        </section>
    );
}
