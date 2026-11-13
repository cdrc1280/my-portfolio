import { Head } from '@inertiajs/react';
import { BriefcaseBusiness, Cpu, Lightbulb } from 'lucide-react';
import {
    AdminSplit,
    BreakdownList,
    countBy,
    FactList,
    SidePanel,
    StatGrid,
    TipList,
} from '@/components/custom/admin-split';
import { PageHeader } from '@/components/custom/page-header';
import { SortableCrudList } from '@/components/custom/sortable-crud-list';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { formatMonth, formatPeriod, formatYearRange } from '@/lib/format';
import { destroy as destroyCertificate, reorder as reorderCertificates } from '@/routes/admin/certificates';
import { destroy as destroyEducation, reorder as reorderEducation } from '@/routes/admin/education';
import { destroy as destroyExperience, index, reorder as reorderExperience } from '@/routes/admin/experience';
import type { SelectOption } from '@/types';
import type { Certificate, Education, EmploymentType, Experience } from '@/types/domains/experience';
import { CertificateDialog } from './certificate-dialog';
import { EducationDialog } from './education-dialog';
import { ExperienceDialog } from './experience-dialog';

type Props = {
    experiences: Experience[];
    education: Education[];
    certificates: Certificate[];
    employmentTypes: SelectOption<EmploymentType>[];
};

/** Whole years between the earliest start and now (or the latest end). */
function yearsOfExperience(experiences: Experience[]): number {
    if (experiences.length === 0) return 0;

    const toMonths = (month: string) => {
        const [year, index] = month.split('-').map(Number);

        return year * 12 + index - 1;
    };
    const now = new Date();
    const start = Math.min(...experiences.map((item) => toMonths(item.started_on)));
    const end = experiences.some((item) => item.is_current || !item.ended_on)
        ? now.getFullYear() * 12 + now.getMonth()
        : Math.max(...experiences.map((item) => toMonths(item.ended_on as string)));

    return Math.max(0, Math.floor((end - start) / 12));
}

export default function CareerIndex({ experiences, education, certificates, employmentTypes }: Props) {
    const current = experiences.find((item) => item.is_current || !item.ended_on);
    const technologies = countBy(
        experiences.flatMap((item) => item.technologies),
        (tech) => tech,
    ).slice(0, 8);

    const aside = (
        <>
            <SidePanel title="Career at a glance" icon={BriefcaseBusiness}>
                <StatGrid
                    stats={[
                        { label: 'Years', value: `${yearsOfExperience(experiences)}+` },
                        { label: 'Roles', value: experiences.length },
                        { label: 'Education', value: education.length },
                        { label: 'Certificates', value: certificates.length },
                    ]}
                />
                <div className="mt-4">
                    <FactList
                        facts={[
                            { term: 'Current', value: current ? current.role : 'None' },
                            {
                                term: 'Remote roles',
                                value: `${experiences.filter((item) => item.is_remote).length} of ${experiences.length}`,
                            },
                            {
                                term: 'Confidential',
                                value: experiences.filter((item) => item.is_confidential).length,
                            },
                        ]}
                    />
                </div>
            </SidePanel>
            <SidePanel title="Most used tech" icon={Cpu}>
                <BreakdownList items={technologies} empty="Add technologies to a role to see them here." />
            </SidePanel>
            <SidePanel title="Tips" icon={Lightbulb}>
                <TipList
                    tips={[
                        'The order here is the order on the About page. Most recent first reads best.',
                        'Confidential roles show as "Confidential client" publicly.',
                        'Lead highlights with outcomes and numbers, not duties.',
                    ]}
                />
            </SidePanel>
        </>
    );

    return (
        <>
            <Head title="Experience" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Experience"
                    description="Work history, education and certifications. Drag to reorder."
                />

                <AdminSplit aside={aside}>
                    <Tabs defaultValue="work">
                        <TabsList>
                            <TabsTrigger value="work">Work ({experiences.length})</TabsTrigger>
                            <TabsTrigger value="education">Education ({education.length})</TabsTrigger>
                            <TabsTrigger value="certificates">Certificates ({certificates.length})</TabsTrigger>
                        </TabsList>

                        <TabsContent value="work" className="pt-4">
                            <SortableCrudList
                                items={experiences}
                                noun="role"
                                reorderUrl={reorderExperience.url()}
                                destroyUrl={(item) => destroyExperience.url(item.id)}
                                itemLabel={(item) => `${item.role} at ${item.company ?? 'confidential client'}`}
                                renderSummary={(item) => (
                                    <>
                                        <p className="truncate font-medium">
                                            {item.role}{' '}
                                            <span className="font-normal text-muted-foreground">
                                                · {item.company ?? 'Confidential client'}
                                                {item.is_confidential && ' (confidential)'}
                                            </span>
                                        </p>
                                        <p className="text-sm text-muted-foreground">
                                            {formatPeriod(item.started_on, item.ended_on)}
                                        </p>
                                    </>
                                )}
                                renderDialog={(trigger, item) => (
                                    <ExperienceDialog
                                        experience={item}
                                        employmentTypes={employmentTypes}
                                        trigger={trigger}
                                    />
                                )}
                            />
                        </TabsContent>

                        <TabsContent value="education" className="pt-4">
                            <SortableCrudList
                                items={education}
                                noun="education"
                                reorderUrl={reorderEducation.url()}
                                destroyUrl={(item) => destroyEducation.url(item.id)}
                                itemLabel={(item) => `${item.qualification}, ${item.institution}`}
                                renderSummary={(item) => (
                                    <>
                                        <p className="truncate font-medium">{item.qualification}</p>
                                        <p className="truncate text-sm text-muted-foreground">
                                            {item.institution} · {formatYearRange(item.started_on, item.ended_on)}
                                        </p>
                                    </>
                                )}
                                renderDialog={(trigger, item) => <EducationDialog education={item} trigger={trigger} />}
                            />
                        </TabsContent>

                        <TabsContent value="certificates" className="pt-4">
                            <SortableCrudList
                                items={certificates}
                                noun="certificate"
                                reorderUrl={reorderCertificates.url()}
                                destroyUrl={(item) => destroyCertificate.url(item.id)}
                                itemLabel={(item) => item.name}
                                renderSummary={(item) => (
                                    <>
                                        <p className="truncate font-medium">{item.name}</p>
                                        <p className="truncate text-sm text-muted-foreground">
                                            {[item.issuer, formatMonth(item.issued_on)].filter(Boolean).join(' · ')}
                                        </p>
                                    </>
                                )}
                                renderDialog={(trigger, item) => (
                                    <CertificateDialog certificate={item} trigger={trigger} />
                                )}
                            />
                        </TabsContent>
                    </Tabs>
                </AdminSplit>
            </div>
        </>
    );
}

CareerIndex.layout = {
    breadcrumbs: [{ title: 'Experience', href: index() }],
};
