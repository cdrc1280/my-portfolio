import { Head } from '@inertiajs/react';
import { Lightbulb, Quote } from 'lucide-react';
import { AdminSplit, SidePanel, StatGrid, TipList } from '@/components/custom/admin-split';
import { PageHeader } from '@/components/custom/page-header';
import { SortableCrudList } from '@/components/custom/sortable-crud-list';
import { Badge } from '@/components/ui/badge';
import { destroy, index, reorder } from '@/routes/admin/testimonials';
import type { SelectOption } from '@/types';
import type { AdminTestimonial } from '@/types/domains/testimonials';
import { TestimonialDialog } from './testimonial-dialog';

type Props = {
    testimonials: AdminTestimonial[];
    projects: SelectOption[];
};

export default function TestimonialsIndex({ testimonials, projects }: Props) {
    const published = testimonials.filter((item) => item.has_permission);
    const aside = (
        <>
            <SidePanel title="Overview" icon={Quote}>
                <StatGrid
                    stats={[
                        { label: 'Total', value: testimonials.length },
                        { label: 'Published', value: published.length },
                        { label: 'Featured', value: published.filter((item) => item.is_featured).length },
                        { label: 'Hidden', value: testimonials.length - published.length, hint: 'No permission yet' },
                    ]}
                />
            </SidePanel>
            <SidePanel title="Tips" icon={Lightbulb}>
                <TipList
                    tips={[
                        "Only quotes with the client's permission are ever shown publicly.",
                        'Up to three featured quotes appear on the home page; a project page shows its first linked quote.',
                        'Short, specific quotes about outcomes land better than general praise.',
                    ]}
                />
            </SidePanel>
        </>
    );

    return (
        <>
            <Head title="Testimonials" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Testimonials"
                    description="Client quotes. Hidden until you confirm the client agreed to publication."
                />
                <AdminSplit aside={aside}>
                    <SortableCrudList
                        items={testimonials}
                        noun="testimonial"
                        reorderUrl={reorder.url()}
                        destroyUrl={(item) => destroy.url(item.id)}
                        itemLabel={(item) => item.author_name}
                        renderSummary={(item) => (
                            <>
                                <p className="flex flex-wrap items-center gap-2">
                                    <span className="font-medium">{item.author_name}</span>
                                    {!item.has_permission && <Badge variant="outline">Hidden: no permission</Badge>}
                                    {item.has_permission && item.is_featured && (
                                        <Badge variant="secondary">Featured</Badge>
                                    )}
                                </p>
                                <p className="truncate text-sm text-muted-foreground">{item.quote}</p>
                            </>
                        )}
                        renderDialog={(trigger, item) => (
                            <TestimonialDialog testimonial={item} projects={projects} trigger={trigger} />
                        )}
                    />
                </AdminSplit>
            </div>
        </>
    );
}

TestimonialsIndex.layout = {
    breadcrumbs: [{ title: 'Testimonials', href: index() }],
};
