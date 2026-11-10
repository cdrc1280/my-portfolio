import type { SocialPlatform } from '@/types';

/** App\Http\Resources\Admin\ProfileResource */
export type AdminProfile = {
    name: string;
    headline: string;
    tagline: string | null;
    bio: string | null;
    location: string | null;
    email: string | null;
    phone: string | null;
    availability: string | null;
    avatar_url: string | null;
    resume_url: string | null;
    resume_updated_at: string | null;
};

/** App\Http\Resources\SocialLinkResource */
export type SocialLink = {
    id: number;
    platform: SocialPlatform;
    label: string;
    url: string;
};
