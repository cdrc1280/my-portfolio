import { useForm } from '@inertiajs/react';
import { FileText, X } from 'lucide-react';
import type { FormEvent } from 'react';
import { FormField } from '@/components/shared/form-field';
import { ImageUploadField } from '@/components/shared/image-upload-field';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Spinner } from '@/components/ui/spinner';
import { Textarea } from '@/components/ui/textarea';
import type { AdminProfile } from '@/features/profile/types';
import { formatDate } from '@/lib/format';
import { update } from '@/routes/admin/profile';

type ProfileFormData = {
    name: string;
    headline: string;
    tagline: string;
    bio: string;
    location: string;
    email: string;
    phone: string;
    availability: string;
    avatar: File | null;
    remove_avatar: boolean;
    resume: File | null;
    remove_resume: boolean;
};

export function ProfileForm({ profile }: { profile: AdminProfile }) {
    const form = useForm<ProfileFormData>({
        name: profile.name,
        headline: profile.headline,
        tagline: profile.tagline ?? '',
        bio: profile.bio ?? '',
        location: profile.location ?? '',
        email: profile.email ?? '',
        phone: profile.phone ?? '',
        availability: profile.availability ?? '',
        avatar: null,
        remove_avatar: false,
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
            onSuccess: () => form.reset('avatar', 'remove_avatar', 'resume', 'remove_resume'),
        });
    };

    const hasResume = profile.resume_url !== null && !data.remove_resume;

    return (
        <form onSubmit={submit} className="grid gap-6" noValidate>
            <Card>
                <CardHeader>
                    <CardTitle>Identity</CardTitle>
                    <CardDescription>Shown in the hero, header and search results.</CardDescription>
                </CardHeader>
                <CardContent className="grid gap-5 sm:grid-cols-2">
                    <FormField label="Name" htmlFor="name" error={errors.name}>
                        <Input id="name" value={data.name} onChange={(e) => setData('name', e.target.value)} />
                    </FormField>
                    <FormField
                        label="Headline"
                        htmlFor="headline"
                        error={errors.headline}
                        hint="Your role, e.g. Full-stack developer"
                    >
                        <Input
                            id="headline"
                            value={data.headline}
                            onChange={(e) => setData('headline', e.target.value)}
                        />
                    </FormField>
                    <FormField
                        label="Pitch"
                        htmlFor="tagline"
                        error={errors.tagline}
                        className="sm:col-span-2"
                        hint="One or two sentences under your name."
                    >
                        <Textarea
                            id="tagline"
                            rows={3}
                            value={data.tagline}
                            onChange={(e) => setData('tagline', e.target.value)}
                        />
                    </FormField>
                    <FormField
                        label="Availability"
                        htmlFor="availability"
                        error={errors.availability}
                        hint="e.g. Open to new opportunities"
                    >
                        <Input
                            id="availability"
                            value={data.availability}
                            onChange={(e) => setData('availability', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Location" htmlFor="location" error={errors.location}>
                        <Input
                            id="location"
                            value={data.location}
                            onChange={(e) => setData('location', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Public email" htmlFor="email" error={errors.email}>
                        <Input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                        />
                    </FormField>
                    <FormField label="Phone" htmlFor="phone" error={errors.phone}>
                        <Input
                            id="phone"
                            type="tel"
                            value={data.phone}
                            onChange={(e) => setData('phone', e.target.value)}
                        />
                    </FormField>
                    <div className="sm:col-span-2">
                        <ImageUploadField
                            label="Photo"
                            currentUrl={profile.avatar_url}
                            file={data.avatar}
                            removed={data.remove_avatar}
                            onFileChange={(file) => setData('avatar', file)}
                            onRemovedChange={(removed) => setData('remove_avatar', removed)}
                            error={errors.avatar}
                            hint="Square works best. JPG, PNG or WebP, up to 2 MB."
                        />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>About</CardTitle>
                    <CardDescription>The body of the About page. Markdown supported.</CardDescription>
                </CardHeader>
                <CardContent>
                    <FormField label="Bio" htmlFor="bio" error={errors.bio}>
                        <Textarea
                            id="bio"
                            rows={12}
                            className="font-mono text-sm"
                            value={data.bio}
                            onChange={(e) => setData('bio', e.target.value)}
                        />
                    </FormField>
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

            <div>
                <Button type="submit" disabled={form.processing}>
                    {form.processing && <Spinner />}
                    Save profile
                </Button>
            </div>
        </form>
    );
}
