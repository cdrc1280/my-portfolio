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

/** App\Domains\Profile\Enums\SocialPlatform */
export type SocialPlatform =
    'github' | 'linkedin' | 'upwork' | 'onlinejobs' | 'x' | 'email' | 'website' | 'youtube' | 'dribbble';

/** App\Domains\Profile\Enums\AvailabilityStatus */
export type AvailabilityStatus = 'available' | 'limited' | 'unavailable';

/** App\Domains\Tags\Enums\TagCategory: groups tags in the skills section. */
export type TagCategory = 'backend' | 'frontend' | 'database' | 'devops_tools' | 'testing' | 'ai_tools';

/** A title + description row (engagement types, process steps). */
export type TitledItem = { title: string; description: string };

/** App\Domains\Profile\DataTransferObjects\SiteProfileData::toArray() — shared as `site` on every page. */
export type SiteProfile = {
    name: string;
    /** Role title. */
    headline: string;
    /** Value statement. */
    tagline: string | null;
    short_bio: string | null;
    location: string | null;
    timezone: string | null;
    email: string | null;
    availability: { status: AvailabilityStatus; label: string; note: string | null };
    working_hours: string | null;
    response_time: string | null;
    booking_url: string | null;
    avatar_url: string | null;
    resume_url: string | null;
    blog_enabled: boolean;
    assistant_enabled: boolean;
    seo: { title: string | null; description: string | null; image_url: string | null };
    socials: Array<{ platform: SocialPlatform; label: string; url: string }>;
};

/** A paginated Laravel ResourceCollection (LengthAwarePaginator). */
export type Paginated<TItem> = {
    data: TItem[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        links: Array<{ url: string | null; label: string; active: boolean }>;
        path: string;
        per_page: number;
        to: number | null;
        total: number;
    };
};
