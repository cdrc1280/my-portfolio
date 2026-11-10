/*
 * Types shared across features. Each mirrors a Laravel API Resource exactly;
 * the PHP class is named in the doc comment so the two can be kept in sync.
 */

/** App\Http\Resources\TagResource */
export type Tag = {
    id: number;
    name: string;
    slug: string;
};

/** Enum options produced by the backend (e.g. ProjectStatus::options()). */
export type SelectOption<TValue extends string = string> = {
    value: TValue;
    label: string;
};

/** App\Domain\Profile\Enums\SocialPlatform */
export type SocialPlatform = 'github' | 'linkedin' | 'x' | 'email' | 'website' | 'youtube' | 'dribbble';

/** App\Domain\Profile\DataTransferObjects\SiteProfileData::toArray() — shared as `site` on every page. */
export type SiteProfile = {
    name: string;
    headline: string;
    tagline: string | null;
    location: string | null;
    email: string | null;
    availability: string | null;
    avatar_url: string | null;
    resume_url: string | null;
    socials: Array<{ platform: SocialPlatform; label: string; url: string }>;
};
