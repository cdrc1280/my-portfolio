import { useForm } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { succeeded } from '@/lib/form-result';
import { store, update } from '@/routes/admin/experience';
import type { SelectOption } from '@/types';
import type { EmploymentType, Experience } from '@/types/domains/experience';

type ExperienceDialogProps = {
    experience?: Experience;
    employmentTypes: SelectOption<EmploymentType>[];
    trigger: ReactNode;
};

/** Select value for "no employment type" (Radix Select items can't use an empty string). */
const NOT_STATED = 'not_stated';

export function ExperienceDialog({ experience, employmentTypes, trigger }: ExperienceDialogProps) {
    const [open, setOpen] = useState(false);
    const form = useForm({
        company: experience?.company ?? '',
        company_url: experience?.company_url ?? '',
        is_confidential: experience?.is_confidential ?? false,
        role: experience?.role ?? '',
        location: experience?.location ?? '',
        is_remote: experience?.is_remote ?? false,
        employment_type: (experience?.employment_type ?? '') as EmploymentType | '',
        started_on: experience?.started_on ?? '',
        ended_on: experience?.ended_on ?? '',
        summary: experience?.summary ?? '',
        highlights: experience?.highlights ?? [],
        technologies: experience?.technologies ?? [],
    });
    const { data, setData, errors } = form;
    const isCurrent = data.ended_on === '';

    const submit = () =>
        form.submit(experience ? update(experience.id) : store(), {
            preserveScroll: true,
            onSuccess: (page) => {
                if (!succeeded(page)) {
                    return;
                }

                setOpen(false);
                if (!experience) {
                    form.reset();
                }
            },
        });

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={experience ? 'Edit role' : 'Add role'}
            processing={form.processing}
            onSubmit={submit}
            size="lg"
        >
            <div className="grid gap-4 sm:grid-cols-2">
                <FormField required label="Role" htmlFor="exp-role" error={errors.role}>
                    <Input id="exp-role" value={data.role} onChange={(e) => setData('role', e.target.value)} />
                </FormField>
                <FormField
                    required={!data.is_confidential}
                    label="Company"
                    htmlFor="exp-company"
                    error={errors.company}
                    hint={data.is_confidential ? 'Optional. Shown publicly as “Confidential client”.' : undefined}
                >
                    <Input id="exp-company" value={data.company} onChange={(e) => setData('company', e.target.value)} />
                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="exp-confidential"
                            checked={data.is_confidential}
                            onCheckedChange={(checked) => setData('is_confidential', checked === true)}
                        />
                        <Label htmlFor="exp-confidential" className="font-normal">
                            Confidential client
                        </Label>
                    </div>
                </FormField>
                <FormField label="Employment type" htmlFor="exp-type" error={errors.employment_type}>
                    <Select
                        value={data.employment_type || NOT_STATED}
                        onValueChange={(value) =>
                            setData('employment_type', value === NOT_STATED ? '' : (value as EmploymentType))
                        }
                    >
                        <SelectTrigger id="exp-type">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value={NOT_STATED}>Not stated</SelectItem>
                            {employmentTypes.map((type) => (
                                <SelectItem key={type.value} value={type.value}>
                                    {type.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </FormField>
                <FormField label="Location" htmlFor="exp-location" error={errors.location}>
                    <Input
                        id="exp-location"
                        value={data.location}
                        disabled={data.is_remote}
                        onChange={(e) => setData('location', e.target.value)}
                    />
                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="exp-remote"
                            checked={data.is_remote}
                            onCheckedChange={(checked) => setData('is_remote', checked === true)}
                        />
                        <Label htmlFor="exp-remote" className="font-normal">
                            Remote
                        </Label>
                    </div>
                </FormField>
                <FormField required label="Started" htmlFor="exp-start" error={errors.started_on}>
                    <Input
                        id="exp-start"
                        type="month"
                        value={data.started_on}
                        onChange={(e) => setData('started_on', e.target.value)}
                    />
                </FormField>
                <FormField label="Ended" htmlFor="exp-end" error={errors.ended_on}>
                    <Input
                        id="exp-end"
                        type="month"
                        value={data.ended_on}
                        disabled={isCurrent}
                        onChange={(e) => setData('ended_on', e.target.value)}
                    />
                    <div className="flex items-center gap-2">
                        <Checkbox
                            id="exp-current"
                            checked={isCurrent}
                            onCheckedChange={(checked) =>
                                setData('ended_on', checked === true ? '' : data.started_on || '')
                            }
                        />
                        <Label htmlFor="exp-current" className="font-normal">
                            Ongoing
                        </Label>
                    </div>
                </FormField>
                <FormField
                    label="Company website"
                    htmlFor="exp-url"
                    error={errors.company_url}
                    className="sm:col-span-2"
                >
                    <Input
                        id="exp-url"
                        type="url"
                        value={data.company_url}
                        onChange={(e) => setData('company_url', e.target.value)}
                    />
                </FormField>
            </div>
            <FormField label="Summary" htmlFor="exp-summary" error={errors.summary}>
                <Textarea
                    id="exp-summary"
                    rows={3}
                    value={data.summary}
                    onChange={(e) => setData('summary', e.target.value)}
                />
            </FormField>
            <FormField label="Highlights" htmlFor="exp-highlights" error={errors.highlights} hint="One per line.">
                <Textarea
                    id="exp-highlights"
                    rows={5}
                    value={data.highlights.join('\n')}
                    onChange={(e) => setData('highlights', e.target.value.split('\n'))}
                />
            </FormField>
            <FormField label="Technologies" htmlFor="exp-tech" error={errors.technologies} hint="Comma separated.">
                <Input
                    id="exp-tech"
                    value={data.technologies.join(', ')}
                    onChange={(e) =>
                        setData(
                            'technologies',
                            e.target.value.split(',').map((item) => item.trimStart()),
                        )
                    }
                />
            </FormField>
        </FormDialog>
    );
}
