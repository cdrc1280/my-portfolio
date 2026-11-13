import type { ReactNode } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { ImageUploadField } from '@/components/custom/image-upload-field';
import { Input } from '@/components/ui/input';
import { useDialogForm } from '@/hooks/use-dialog-form';
import { store, update } from '@/routes/admin/certificates';
import type { Certificate } from '@/types/domains/experience';

type CertificateFormData = {
    name: string;
    issuer: string;
    issued_on: string;
    credential_url: string;
    badge: File | null;
    remove_badge: boolean;
};

export function CertificateDialog({ certificate, trigger }: { certificate?: Certificate; trigger: ReactNode }) {
    const { open, setOpen, form, submit } = useDialogForm<CertificateFormData>(
        {
            name: certificate?.name ?? '',
            issuer: certificate?.issuer ?? '',
            issued_on: certificate?.issued_on ?? '',
            credential_url: certificate?.credential_url ?? '',
            badge: null,
            remove_badge: false,
        },
        {
            storeUrl: store.url(),
            updateUrl: certificate ? update.url(certificate.id) : undefined,
            multipart: true,
        },
    );
    const { data, setData, errors } = form;

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={certificate ? 'Edit certificate' : 'Add certificate'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField required label="Title" htmlFor="cert-name" error={errors.name}>
                <Input id="cert-name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
            </FormField>
            <FormField label="Issuer" htmlFor="cert-issuer" error={errors.issuer}>
                <Input id="cert-issuer" value={data.issuer} onChange={(e) => setData('issuer', e.target.value)} />
            </FormField>
            <FormField required label="Issued" htmlFor="cert-issued" error={errors.issued_on}>
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
            <ImageUploadField
                label="Badge"
                currentUrl={certificate?.badge_url ?? null}
                file={data.badge}
                removed={data.remove_badge}
                onFileChange={(file) => setData('badge', file)}
                onRemovedChange={(removed) => setData('remove_badge', removed)}
                error={errors.badge}
                hint="Square PNG or WebP, up to 1 MB."
            />
        </FormDialog>
    );
}
