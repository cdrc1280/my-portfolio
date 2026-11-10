import { useForm } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { FormDialog } from '@/components/shared/form-dialog';
import { FormField } from '@/components/shared/form-field';
import { Input } from '@/components/ui/input';
import type { Certificate } from '@/features/experience/types';
import { store, update } from '@/routes/admin/certificates';

export function CertificateDialog({ certificate, trigger }: { certificate?: Certificate; trigger: ReactNode }) {
    const [open, setOpen] = useState(false);
    const form = useForm({
        name: certificate?.name ?? '',
        issuer: certificate?.issuer ?? '',
        issued_on: certificate?.issued_on ?? '',
        credential_url: certificate?.credential_url ?? '',
    });
    const { data, setData, errors } = form;

    const submit = () =>
        form.submit(certificate ? update(certificate.id) : store(), {
            preserveScroll: true,
            onSuccess: () => {
                setOpen(false);
                if (!certificate) {
                    form.reset();
                }
            },
        });

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={certificate ? 'Edit certificate' : 'Add certificate'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField label="Name" htmlFor="cert-name" error={errors.name}>
                <Input id="cert-name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
            </FormField>
            <FormField label="Issuer" htmlFor="cert-issuer" error={errors.issuer}>
                <Input id="cert-issuer" value={data.issuer} onChange={(e) => setData('issuer', e.target.value)} />
            </FormField>
            <FormField label="Issued" htmlFor="cert-issued" error={errors.issued_on}>
                <Input
                    id="cert-issued"
                    type="month"
                    value={data.issued_on}
                    onChange={(e) => setData('issued_on', e.target.value)}
                />
            </FormField>
            <FormField label="Credential URL" htmlFor="cert-url" error={errors.credential_url}>
                <Input
                    id="cert-url"
                    type="url"
                    value={data.credential_url}
                    onChange={(e) => setData('credential_url', e.target.value)}
                />
            </FormField>
        </FormDialog>
    );
}
