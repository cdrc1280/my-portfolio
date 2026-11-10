import { Head } from '@inertiajs/react';
import { PageHeader } from '@/components/shared/page-header';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { CareerSection } from '@/features/experience/components/career-section';
import { CertificateDialog } from '@/features/experience/components/certificate-dialog';
import { EducationDialog } from '@/features/experience/components/education-dialog';
import { ExperienceDialog } from '@/features/experience/components/experience-dialog';
import type { Certificate, Education, EmploymentType, Experience } from '@/features/experience/types';
import { formatMonth, formatPeriod } from '@/lib/format';
import { destroy as destroyCertificate, reorder as reorderCertificates } from '@/routes/admin/certificates';
import { destroy as destroyEducation, reorder as reorderEducation } from '@/routes/admin/education';
import { destroy as destroyExperience, index, reorder as reorderExperience } from '@/routes/admin/experience';
import type { SelectOption } from '@/types';

type Props = {
    experiences: Experience[];
    education: Education[];
    certificates: Certificate[];
    employmentTypes: SelectOption<EmploymentType>[];
};

export default function CareerIndex({ experiences, education, certificates, employmentTypes }: Props) {
    return (
        <>
            <Head title="Experience" />
            <div className="flex max-w-4xl flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Experience"
                    description="Work history, education and certifications. Drag to reorder."
                />

                <Tabs defaultValue="work">
                    <TabsList>
                        <TabsTrigger value="work">Work ({experiences.length})</TabsTrigger>
                        <TabsTrigger value="education">Education ({education.length})</TabsTrigger>
                        <TabsTrigger value="certificates">Certificates ({certificates.length})</TabsTrigger>
                    </TabsList>

                    <TabsContent value="work" className="pt-4">
                        <CareerSection
                            items={experiences}
                            noun="role"
                            reorderUrl={reorderExperience.url()}
                            destroyUrl={(item) => destroyExperience.url(item.id)}
                            itemLabel={(item) => `${item.role} at ${item.company}`}
                            renderSummary={(item) => (
                                <>
                                    <p className="truncate font-medium">
                                        {item.role}{' '}
                                        <span className="font-normal text-muted-foreground">· {item.company}</span>
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
                        <CareerSection
                            items={education}
                            noun="education"
                            reorderUrl={reorderEducation.url()}
                            destroyUrl={(item) => destroyEducation.url(item.id)}
                            itemLabel={(item) => `${item.qualification}, ${item.institution}`}
                            renderSummary={(item) => (
                                <>
                                    <p className="truncate font-medium">{item.qualification}</p>
                                    <p className="truncate text-sm text-muted-foreground">
                                        {item.institution} · {formatPeriod(item.started_on, item.ended_on)}
                                    </p>
                                </>
                            )}
                            renderDialog={(trigger, item) => <EducationDialog education={item} trigger={trigger} />}
                        />
                    </TabsContent>

                    <TabsContent value="certificates" className="pt-4">
                        <CareerSection
                            items={certificates}
                            noun="certificate"
                            reorderUrl={reorderCertificates.url()}
                            destroyUrl={(item) => destroyCertificate.url(item.id)}
                            itemLabel={(item) => item.name}
                            renderSummary={(item) => (
                                <>
                                    <p className="truncate font-medium">{item.name}</p>
                                    <p className="truncate text-sm text-muted-foreground">
                                        {item.issuer} · {formatMonth(item.issued_on)}
                                    </p>
                                </>
                            )}
                            renderDialog={(trigger, item) => <CertificateDialog certificate={item} trigger={trigger} />}
                        />
                    </TabsContent>
                </Tabs>
            </div>
        </>
    );
}

CareerIndex.layout = {
    breadcrumbs: [{ title: 'Experience', href: index() }],
};
