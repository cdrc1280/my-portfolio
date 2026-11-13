import { useForm } from '@inertiajs/react';
import type { ReactNode } from 'react';
import { useState } from 'react';
import { FormDialog } from '@/components/custom/form-dialog';
import { FormField } from '@/components/custom/form-field';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { succeeded } from '@/lib/form-result';
import { store, update } from '@/routes/admin/social-links';
import type { SelectOption, SocialPlatform } from '@/types';
import type { SocialLink } from '@/types/domains/profile';

type SocialLinkDialogProps = {
    link?: SocialLink;
    platforms: SelectOption<SocialPlatform>[];
    trigger: ReactNode;
};

export function SocialLinkDialog({ link, platforms, trigger }: SocialLinkDialogProps) {
    const [open, setOpen] = useState(false);
    const form = useForm({
        platform: link?.platform ?? ('github' as SocialPlatform),
        url: link?.url.replace(/^mailto:/, '') ?? '',
    });

    const submit = () =>
        form.submit(link ? update(link.id) : store(), {
            preserveScroll: true,
            onSuccess: (page) => {
                if (!succeeded(page)) {
                    return;
                }

                setOpen(false);
                if (!link) {
                    form.reset();
                }
            },
        });

    return (
        <FormDialog
            open={open}
            onOpenChange={setOpen}
            trigger={trigger}
            title={link ? 'Edit link' : 'Add link'}
            processing={form.processing}
            onSubmit={submit}
        >
            <FormField required label="Platform" htmlFor="social-platform" error={form.errors.platform}>
                <Select
                    value={form.data.platform}
                    onValueChange={(value) => form.setData('platform', value as SocialPlatform)}
                >
                    <SelectTrigger id="social-platform">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {platforms.map((platform) => (
                            <SelectItem key={platform.value} value={platform.value}>
                                {platform.label}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </FormField>
            <FormField
                required
                label={form.data.platform === 'email' ? 'Email address' : 'URL'}
                htmlFor="social-url"
                error={form.errors.url}
            >
                <Input
                    id="social-url"
                    type={form.data.platform === 'email' ? 'email' : 'url'}
                    value={form.data.url}
                    onChange={(e) => form.setData('url', e.target.value)}
                />
            </FormField>
        </FormDialog>
    );
}
