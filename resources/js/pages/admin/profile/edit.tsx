import { Head } from '@inertiajs/react';
import { PageHeader } from '@/components/shared/page-header';
import { ProfileForm } from '@/features/profile/components/profile-form';
import { SocialLinksEditor } from '@/features/profile/components/social-links-editor';
import type { AdminProfile, SocialLink } from '@/features/profile/types';
import { edit } from '@/routes/admin/profile';
import type { SelectOption, SocialPlatform } from '@/types';

type Props = {
    profile: AdminProfile;
    socialLinks: SocialLink[];
    platforms: SelectOption<SocialPlatform>[];
};

export default function EditProfile({ profile, socialLinks, platforms }: Props) {
    return (
        <>
            <Head title="Site profile" />
            <div className="flex max-w-3xl flex-col gap-6 p-4 md:p-6">
                <PageHeader title="Site profile" description="Your public identity across the portfolio." />
                <ProfileForm profile={profile} />
                <SocialLinksEditor links={socialLinks} platforms={platforms} />
            </div>
        </>
    );
}

EditProfile.layout = {
    breadcrumbs: [{ title: 'Site profile', href: edit() }],
};
