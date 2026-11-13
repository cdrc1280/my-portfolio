import { Head, Link } from '@inertiajs/react';
import { CheckCircle2, Circle, ExternalLink, IdCard, ListChecks } from 'lucide-react';
import { AdminSplit, SidePanel } from '@/components/custom/admin-split';
import { AvailabilityBadge } from '@/components/custom/availability-badge';
import { PageHeader } from '@/components/custom/page-header';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { home } from '@/routes';
import { edit } from '@/routes/admin/profile';
import type { AvailabilityStatus, SelectOption, SocialPlatform } from '@/types';
import type { AdminProfile, SocialLink } from '@/types/domains/profile';
import { ProfileForm } from './profile-form';
import { SocialLinksEditor } from './social-links-editor';

type Props = {
    profile: AdminProfile;
    socialLinks: SocialLink[];
    platforms: SelectOption<SocialPlatform>[];
    availabilityStatuses: SelectOption<AvailabilityStatus>[];
};

export default function EditProfile({ profile, socialLinks, platforms, availabilityStatuses }: Props) {
    const initials = profile.name
        .split(' ')
        .map((part) => part[0])
        .slice(0, 2)
        .join('');

    const checklist = [
        { label: 'Avatar', done: profile.avatar_url !== null },
        { label: 'Short bio', done: Boolean(profile.short_bio) },
        { label: 'Full bio', done: Boolean(profile.bio) },
        { label: 'Résumé uploaded', done: profile.resume_url !== null },
        { label: 'Booking link', done: Boolean(profile.booking_url) },
        { label: 'SEO title & description', done: Boolean(profile.seo_title && profile.seo_description) },
        { label: 'Social image', done: profile.og_image_url !== null },
        { label: 'At least one social link', done: socialLinks.length > 0 },
    ];
    const complete = checklist.filter((item) => item.done).length;

    const aside = (
        <>
            <SidePanel
                title="Public card"
                icon={IdCard}
                action={
                    <Button variant="ghost" size="sm" asChild>
                        <Link href={home()}>
                            <ExternalLink aria-hidden /> Site
                        </Link>
                    </Button>
                }
            >
                <div className="flex items-center gap-3">
                    <Avatar className="size-12">
                        {profile.avatar_url && <AvatarImage src={profile.avatar_url} alt="" />}
                        <AvatarFallback>{initials}</AvatarFallback>
                    </Avatar>
                    <div className="min-w-0">
                        <p className="truncate font-medium">{profile.name}</p>
                        <p className="truncate text-sm text-muted-foreground">{profile.headline}</p>
                    </div>
                </div>
                {profile.tagline && <p className="mt-3 text-sm text-muted-foreground">{profile.tagline}</p>}
                <AvailabilityBadge className="mt-3 text-muted-foreground" />
                <p className="mt-2 text-xs text-muted-foreground">Reflects the last saved settings.</p>
            </SidePanel>
            <SidePanel
                title="Completeness"
                icon={ListChecks}
                action={
                    <span className="text-xs text-muted-foreground tabular-nums">
                        {complete}/{checklist.length}
                    </span>
                }
            >
                <div className="mb-3 h-1.5 overflow-hidden rounded-full bg-muted">
                    <div
                        className="h-full rounded-full bg-brand"
                        style={{ width: `${(complete / checklist.length) * 100}%` }}
                    />
                </div>
                <ul className="grid gap-1.5 text-sm">
                    {checklist.map((item) => (
                        <li key={item.label} className="flex items-center gap-2">
                            {item.done ? (
                                <CheckCircle2 className="size-4 text-brand" aria-hidden />
                            ) : (
                                <Circle className="size-4 text-muted-foreground" aria-hidden />
                            )}
                            <span className={item.done ? undefined : 'text-muted-foreground'}>{item.label}</span>
                            <span className="sr-only">{item.done ? '(done)' : '(missing)'}</span>
                        </li>
                    ))}
                </ul>
            </SidePanel>
        </>
    );

    return (
        <>
            <Head title="Settings" />
            <div className="flex flex-col gap-6 p-4 md:p-6">
                <PageHeader
                    title="Settings"
                    description="Your public identity, availability, SEO defaults and site features."
                />
                <AdminSplit aside={aside}>
                    <ProfileForm profile={profile} availabilityStatuses={availabilityStatuses} />
                    <SocialLinksEditor links={socialLinks} platforms={platforms} />
                </AdminSplit>
            </div>
        </>
    );
}

EditProfile.layout = {
    breadcrumbs: [{ title: 'Settings', href: edit() }],
};
