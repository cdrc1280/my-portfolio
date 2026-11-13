import { useForm } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { succeeded } from '@/lib/form-result';
import { store, update } from '@/routes/admin/education';
import type { Education } from '@/types/domains/experience';

export function EducationDialog({ education, trigger }: { education?: Education; trigger: ReactNode }) {
    const [open, setOpen] = useState(false);
    const form = useForm({
        institution: education?.institution ?? '',
        qualification: education?.qualification ?? '',
        field_of_study: education?.field_of_study ?? '',
        started_on: education?.started_on ?? '',
        ended_on: education?.ended_on ?? '',
        notes: education?.notes ?? '',
    });
    const { data, setData, errors } = form;

    const submit = () =>
        form.submit(education ? update(education.id) : store(), {
            preserveScroll: true,
            onSuccess: (page) => {
                if (!succeeded(page)) {
                    return;
                }

                setOpen(false);
                if (!education) {
                    form.reset();
                }
            },
        });

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={education ? 'Edit education' : 'Add education'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField required label="Institution" htmlFor="edu-institution" error={errors.institution}>
                <Input
                    id="edu-institution"
                    value={data.institution}
                    onChange={(e) => setData('institution', e.target.value)}
                />
            </FormField>
            <FormField required label="Qualification" htmlFor="edu-qualification" error={errors.qualification}>
                <Input
                    id="edu-qualification"
                    value={data.qualification}
                    onChange={(e) => setData('qualification', e.target.value)}
                    placeholder="e.g. Bachelor of Science"
                />
            </FormField>
            <FormField label="Field of study" htmlFor="edu-field" error={errors.field_of_study}>
                <Input
                    id="edu-field"
                    value={data.field_of_study}
                    onChange={(e) => setData('field_of_study', e.target.value)}
                />
            </FormField>
            <div className="grid gap-4 sm:grid-cols-2">
                <FormField required label="Started" htmlFor="edu-start" error={errors.started_on}>
                    <Input
                        id="edu-start"
                        type="month"
                        value={data.started_on}
                        onChange={(e) => setData('started_on', e.target.value)}
                    />
                </FormField>
                <FormField label="Finished" htmlFor="edu-end" error={errors.ended_on} hint="Leave empty if ongoing.">
                    <Input
                        id="edu-end"
                        type="month"
                        value={data.ended_on}
                        onChange={(e) => setData('ended_on', e.target.value)}
                    />
                </FormField>
            </div>
            <FormField label="Notes" htmlFor="edu-notes" error={errors.notes}>
                <Textarea
                    id="edu-notes"
                    rows={3}
                    value={data.notes}
                    onChange={(e) => setData('notes', e.target.value)}
                />
            </FormField>
        </FormDialog>
    );
}
