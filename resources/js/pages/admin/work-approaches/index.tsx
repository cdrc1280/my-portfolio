import { Head } from '@inertiajs/react';
import { Compass, Lightbulb } from 'lucide-react';
import { AdminSplit, SidePanel, StatGrid, TipList } from '@/components/custom/admin-split';
import { ContentIcon } from '@/components/custom/content-icon';
import { PageHeader } from '@/components/custom/page-header';
import { SortableCrudList } from '@/components/custom/sortable-crud-list';
import { destroy, index, reorder } from '@/routes/admin/work-approaches';
import type { WorkApproach } from '@/types/domains/work-approach';
import { WorkApproachDialog } from './work-approach-dialog';

export default function WorkApproachesIndex({ approaches }: { approaches: WorkApproach[] }) {
    const aside = (
        <>
            <SidePanel title="Overview" icon={Compass}>
                <StatGrid
                    stats={[
                        { label: 'Principles', value: approaches.length },
                        { label: 'With snippet', value: approaches.filter((item) => item.snippet).length },
                    ]}
                />
            </SidePanel>
            <SidePanel title="Tips" icon={Lightbulb}>
                <TipList
                    tips={[
                        'Shown on the home page in this order: lead with the strongest one.',
                        'Even counts or multiples of three fill the home page grid without gaps.',
                        'A short mono snippet makes a principle concrete.',
                    ]}
                />
            </SidePanel>
        </>
    );

    return (
        <>
            <Head title="How I work" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader title="How I work" description="Principles shown on the home page, in this order." />
                <AdminSplit aside={aside}>
                    <SortableCrudList
                        items={approaches}
                        noun="approach"
                        reorderUrl={reorder.url()}
                        destroyUrl={(item) => destroy.url(item.id)}
                        itemLabel={(item) => item.title}
                        renderSummary={(item) => (
                            <div className="flex items-center gap-3">
                                <ContentIcon icon={item.icon} className="size-4 text-muted-foreground" />
                                <div className="min-w-0">
                                    <p className="font-medium">{item.title}</p>
                                    <p className="truncate text-sm text-muted-foreground">{item.description}</p>
                                </div>
                            </div>
                        )}
                        renderDialog={(trigger, item) => <WorkApproachDialog approach={item} trigger={trigger} />}
                    />
                </AdminSplit>
            </div>
        </>
    );
}

WorkApproachesIndex.layout = {
    breadcrumbs: [{ title: 'How I work', href: index() }],
};
