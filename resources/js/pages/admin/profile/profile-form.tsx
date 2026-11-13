import { useForm } from '@inertiajs/react';
import { FileText, X } from 'lucide-react';
import type { FormEvent, ReactNode } from 'react';
import { FormField } from '@/components/custom/form-field';
import { ImageUploadField } from '@/components/custom/image-upload-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { succeeded } from '@/lib/form-result';
import { formatDate } from '@/lib/format';
import { update } from '@/routes/admin/profile';
import type { AvailabilityStatus, SelectOption } from '@/types';
import type { AdminProfile } from '@/types/domains/profile';

type ProfileFormData = {
    name: string;
    headline: string;
    tagline: string;
    short_bio: string;
    bio: string;
    location: string;
    timezone: string;
    email: string;
    phone: string;
    availability_status: AvailabilityStatus;
    availability: string;
    working_hours: string;
    response_time: string;
    booking_url: string;
    currently_learning: string;
    blog_enabled: boolean;
    assistant_enabled: boolean;
    seo_title: string;
    seo_description: string;
    avatar: File | null;
    remove_avatar: boolean;
    og_image: File | null;
    remove_og_image: boolean;
    resume: File | null;
    remove_resume: boolean;
};

type TextKey = {
    [K in keyof ProfileFormData]: ProfileFormData[K] extends string ? K : never;
}[keyof ProfileFormData];

type ProfileFormProps = {
    profile: AdminProfile;
    availabilityStatuses: SelectOption<AvailabilityStatus>[];
};

/** Site-wide settings: identity, bio, availability, contact details, files, SEO defaults and features. */
export function ProfileForm({ profile, availabilityStatuses }: ProfileFormProps) {
    const form = useForm<ProfileFormData>({
        name: profile.name,
        headline: profile.headline,
        tagline: profile.tagline ?? '',
        short_bio: profile.short_bio ?? '',
        bio: profile.bio ?? '',
        location: profile.location ?? '',
        timezone: profile.timezone ?? '',
        email: profile.email ?? '',
        phone: profile.phone ?? '',
        availability_status: profile.availability_status,
        availability: profile.availability ?? '',
        working_hours: profile.working_hours ?? '',
        response_time: profile.response_time ?? '',
        booking_url: profile.booking_url ?? '',
        currently_learning: profile.currently_learning ?? '',
        blog_enabled: profile.blog_enabled,
        assistant_enabled: profile.assistant_enabled,
        seo_title: profile.seo_title ?? '',
        seo_description: profile.seo_description ?? '',
        avatar: null,
        remove_avatar: false,
        og_image: null,
        remove_og_image: false,
        resume: null,
        remove_resume: false,
    });
    const { data, setData, errors } = form;

    const submit = (event: FormEvent) => {
        event.preventDefault();
        form.transform((values) => ({ ...values, _method: 'put' }));
        form.post(update.url(), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: (page) =>
                succeeded(page) &&
                form.reset('avatar', 'remove_avatar', 'og_image', 'remove_og_image', 'resume', 'remove_resume'),
        });
    };

    const text = (
        key: TextKey,
        label: string,
        options: { hint?: ReactNode; type?: string; wide?: boolean; required?: boolean } = {},
    ) => (
        <FormField
            required={options.required}
            label={label}
            htmlFor={key}
            error={errors[key]}
            hint={options.hint}
            className={options.wide ? 'sm:col-span-2' : undefined}
        >
            <Input
                id={key}
                type={options.type ?? 'text'}
                value={data[key]}
                onChange={(event) => setData(key, event.target.value)}
            />
        </FormField>
    );

    const longText = (key: TextKey, label: string, rows: number, hint?: ReactNode, mono = false) => (
        <FormField label={label} htmlFor={key} error={errors[key]} hint={hint} className="sm:col-span-2">
            <Textarea
                id={key}
                rows={rows}
                className={mono ? 'font-mono text-sm' : undefined}
                value={data[key]}
                onChange={(event) => setData(key, event.target.value)}
            />
        </FormField>
    );

    const hasResume = profile.resume_url !== null && !data.remove_resume;

    return (
        <form onSubmit={submit} className="grid gap-6" noValidate>
            <Card>
                <CardHeader>
                    <CardTitle>Identity</CardTitle>
                    <CardDescription>The hero, header and search results.</CardDescription>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2">
                    {text('name', 'Name', { required: true })}
                    {text('headline', 'Role title', { hint: 'e.g. Full-stack developer', required: true })}
                    {longText(
                        'tagline',
                        'Value statement',
                        2,
                        'The hero headline. Wrap words in *asterisks* to highlight them in the accent colour.',
                    )}
                    {text('location', 'Location', { hint: 'City and country is enough.' })}
                    {text('timezone', 'Timezone', { hint: 'e.g. UTC+8 (Philippine time)' })}
                    <div className="sm:col-span-2">
                        <ImageUploadField
                            label="Photo"
                            currentUrl={profile.avatar_url}
                            file={data.avatar}
                            removed={data.remove_avatar}
                            onFileChange={(file) => setData('avatar', file)}
                            onRemovedChange={(removed) => setData('remove_avatar', removed)}
                            error={errors.avatar}
                            hint="Portrait works best. JPG, PNG or WebP, up to 2 MB."
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Bio</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2">
                    {longText('short_bio', 'Short bio', 2, 'One or two sentences under the hero headline.')}
                    {longText('bio', 'Long bio', 10, 'The About page. Markdown supported.', true)}
                    {longText('currently_learning', 'Currently learning', 2, 'Shown on the About page. Optional.')}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Availability & contact</CardTitle>
                    <CardDescription>Shown in the hero, on Services and on Contact.</CardDescription>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2">
                    <FormField
                        required
                        label="Availability"
                        htmlFor="availability_status"
                        error={errors.availability_status}
                    >
                        <Select
                            value={data.availability_status}
                            onValueChange={(value) => setData('availability_status', value as AvailabilityStatus)}
                        >
                            <SelectTrigger id="availability_status" className="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                {availabilityStatuses.map((status) => (
                                    <SelectItem key={status.value} value={status.value}>
                                        {status.label}
                                    </SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </FormField>
                    {text('availability', 'Availability note', { hint: 'e.g. Open to part-time contracts' })}
                    {text('working_hours', 'Working hours', {
                        hint: 'e.g. Evenings, Philippine time; overlaps with US mornings',
                        wide: true,
                    })}
                    {text('response_time', 'Response time', {
                        hint: 'Completes “I reply …”, e.g. within two working days',
                    })}
                    {text('email', 'Public email', { type: 'email' })}
                    {text('booking_url', 'Booking URL', {
                        type: 'url',
                        hint: 'Calendly or similar. Adds a “Book a call” button.',
                    })}
                    {text('phone', 'Phone', { type: 'tel', hint: 'Not shown publicly.' })}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Résumé</CardTitle>
                    <CardDescription>PDF offered for download on the About page.</CardDescription>
                </CardHeader>
                <CardContent className="grid gap-3">
                    {hasResume && !data.resume && (
                        <div className="flex items-center justify-between gap-3 rounded-lg border px-3 py-2 text-sm">
                            <a
                                href={profile.resume_url ?? undefined}
                                className="inline-flex items-center gap-2 hover:underline"
                            >
                                <FileText className="size-4" aria-hidden /> Current résumé
                                {profile.resume_updated_at && (
                                    <span className="text-muted-foreground">
                                        · updated {formatDate(profile.resume_updated_at)}
                                    </span>
                                )}
                            </a>
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                onClick={() => setData('remove_resume', true)}
                            >
                                <X aria-hidden /> Remove
                            </Button>
                        </div>
                    )}
                    <FormField label="Upload a new PDF" htmlFor="resume" error={errors.resume} hint="Up to 5 MB.">
                        <input
                            id="resume"
                            type="file"
                            accept="application/pdf"
                            className="text-sm file:mr-3 file:rounded-md file:border file:bg-background file:px-3 file:py-1.5 file:text-sm file:font-medium"
                            onChange={(e) => {
                                setData('resume', e.target.files?.[0] ?? null);
                                setData('remove_resume', false);
                            }}
                        />
                    </FormField>
                    {data.remove_resume && (
                        <p className="text-sm text-muted-foreground">The résumé will be removed when you save.</p>
                    )}
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>SEO defaults</CardTitle>
                    <CardDescription>Used by any page that doesn’t set its own.</CardDescription>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2">
                    {text('seo_title', 'Default title', { hint: 'Up to 70 characters.', wide: true })}
                    {longText('seo_description', 'Default description', 2, 'Up to 200 characters.')}
                    <div className="sm:col-span-2">
                        <ImageUploadField
                            label="Default social image"
                            currentUrl={profile.og_image_url}
                            file={data.og_image}
                            removed={data.remove_og_image}
                            onFileChange={(file) => setData('og_image', file)}
                            onRemovedChange={(removed) => setData('remove_og_image', removed)}
                            error={errors.og_image}
                            hint="1200 × 630 px works best. JPG, PNG or WebP, up to 2 MB."
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Features</CardTitle>
                </CardHeader>
                <CardContent className="grid gap-5">
                    <div className="flex items-center justify-between gap-4">
                        <div>
                            <Label htmlFor="blog_enabled">Blog</Label>
                            <p className="text-xs text-muted-foreground">
                                When off, the blog leaves the navigation and its pages return 404.
                            </p>
                        </div>
                        <Switch
                            id="blog_enabled"
                            checked={data.blog_enabled}
                            onCheckedChange={(checked) => setData('blog_enabled', checked)}
                        />
                    </div>
                    <div className="flex items-center justify-between gap-4">
                        <div>
                            <Label htmlFor="assistant_enabled">“Ask about me” assistant</Label>
                            <p className="text-xs text-muted-foreground">
                                A chat button on public pages. It answers only from this site&apos;s content, with no AI
                                service and no cost.
                            </p>
                        </div>
                        <Switch
                            id="assistant_enabled"
                            checked={data.assistant_enabled}
                            onCheckedChange={(checked) => setData('assistant_enabled', checked)}
                        />
                    </div>
                </CardContent>
            </Card>

            <div>
                <Button type="submit" disabled={form.processing}>
                    {form.processing && <Spinner />}
                    Save settings
                </Button>
            </div>
        </form>
    );
}
