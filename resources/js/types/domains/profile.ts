import type { AvailabilityStatus, SocialPlatform } from '@/types';

/** App\Domains\*\Resources\AdminProfileResource */
export type AdminProfile = {
    name: string;
    headline: string;
    tagline: string | null;
    short_bio: string | null;
    bio: string | null;
    location: string | null;
    timezone: string | null;
    email: string | null;
    phone: string | null;
    availability_status: AvailabilityStatus;
    availability: string | null;
    working_hours: string | null;
    response_time: string | null;
    booking_url: string | null;
    currently_learning: string | null;
    blog_enabled: boolean;
    assistant_enabled: boolean;
    seo_title: string | null;
    seo_description: string | null;
    avatar_url: string | null;
    og_image_url: string | null;
    resume_url: string | null;
    resume_updated_at: string | null;
};

/** App\Domains\*\Resources\SocialLinkResource */
export type SocialLink = {
    id: number;
    platform: SocialPlatform;
    label: string;
    url: string;
};
