import { Head } from '@inertiajs/react';
import { Lightbulb, Wrench } from 'lucide-react';
import { AdminSplit, BreakdownList, countBy, SidePanel, StatGrid, TipList } from '@/components/custom/admin-split';
import { PageHeader } from '@/components/custom/page-header';
import { SortableCrudList } from '@/components/custom/sortable-crud-list';
import { Badge } from '@/components/ui/badge';
import { destroy, index, reorder } from '@/routes/admin/uses';
import type { UsesItem } from '@/types/domains/uses';
import { UsesItemDialog } from './uses-item-dialog';

export default function UsesIndex({ items }: { items: UsesItem[] }) {
    const categories = [...new Set(items.map((item) => item.category))];

    const aside = (
        <>
            <SidePanel title="Overview" icon={Wrench}>
                <StatGrid
                    stats={[
                        { label: 'Items', value: items.length },
                        { label: 'Categories', value: categories.length },
                        { label: 'With link', value: items.filter((item) => item.url).length },
                        { label: 'Described', value: items.filter((item) => item.description).length },
                    ]}
                />
            </SidePanel>
            <SidePanel title="By category">
                <BreakdownList items={countBy(items, (item) => item.category)} />
            </SidePanel>
            <SidePanel title="Tips" icon={Lightbulb}>
                <TipList
                    tips={[
                        'Categories appear in the order of their first item.',
                        'A one-line "why I use it" makes the page worth reading.',
                    ]}
                />
            </SidePanel>
        </>
    );

    return (
        <>
            <Head title="Uses" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Uses"
                    description="Tools and services for the /uses page. Grouped by category, in this order."
                />
                <AdminSplit aside={aside}>
                    <SortableCrudList
                        items={items}
                        noun="item"
                        reorderUrl={reorder.url()}
                        destroyUrl={(item) => destroy.url(item.id)}
                        itemLabel={(item) => item.name}
                        renderSummary={(item) => (
                            <p className="flex flex-wrap items-center gap-2">
                                <span className="font-medium">{item.name}</span>
                                <Badge variant="secondary">{item.category}</Badge>
                            </p>
                        )}
                        renderDialog={(trigger, item) => (
                            <UsesItemDialog item={item} categories={categories} trigger={trigger} />
                        )}
                    />
                </AdminSplit>
            </div>
        </>
    );
}

UsesIndex.layout = {
    breadcrumbs: [{ title: 'Uses', href: index() }],
};
