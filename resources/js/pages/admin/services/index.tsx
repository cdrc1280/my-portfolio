import { Head } from '@inertiajs/react';
import { BriefcaseBusiness, Lightbulb } from 'lucide-react';
import { AdminSplit, FactList, SidePanel, StatGrid, TipList } from '@/components/custom/admin-split';
import { ContentIcon } from '@/components/custom/content-icon';
import { PageHeader } from '@/components/custom/page-header';
import { SortableCrudList } from '@/components/custom/sortable-crud-list';
import { destroy, index, reorder } from '@/routes/admin/services';
import type { EngagementSettings, Service } from '@/types/domains/services';
import { EngagementSettingsForm } from './engagement-settings-form';
import { ServiceDialog } from './service-dialog';

type Props = {
    services: Service[];
    engagement: EngagementSettings;
};

export default function ServicesIndex({ services, engagement }: Props) {
    const aside = (
        <>
            <SidePanel title="Offer at a glance" icon={BriefcaseBusiness}>
                <StatGrid
                    stats={[
                        { label: 'Services', value: services.length },
                        { label: 'Process steps', value: engagement.process_steps.length },
                    ]}
                />
                <div className="mt-4">
                    <FactList
                        facts={[
                            { term: 'Starting rate', value: engagement.starting_rate || 'Not shown' },
                            { term: 'Engagement types', value: engagement.engagement_types.length },
                        ]}
                    />
                </div>
            </SidePanel>
            <SidePanel title="Tips" icon={Lightbulb}>
                <TipList
                    tips={[
                        'Three to five services is easier to scan than a long list.',
                        'Describe the result a client gets, not the technology.',
                        'A starting rate filters out poor-fit leads before they write.',
                    ]}
                />
            </SidePanel>
        </>
    );

    return (
        <>
            <Head title="Services" />
            <div className="flex flex-col gap-8 p-4 md:p-6">
                <PageHeader title="Services" description="What you offer, how clients can engage, and your process." />
                <AdminSplit aside={aside}>
                    <SortableCrudList
                        items={services}
                        noun="service"
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
                        renderDialog={(trigger, item) => <ServiceDialog service={item} trigger={trigger} />}
                    />
                    <EngagementSettingsForm engagement={engagement} />
                </AdminSplit>
            </div>
        </>
    );
}

ServicesIndex.layout = {
    breadcrumbs: [{ title: 'Services', href: index() }],
};
