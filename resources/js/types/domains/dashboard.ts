import type { AvailabilityStatus, SelectOption } from '@/types';

/** App\Domains\Bookings\DataTransferObjects\BookingData::toArray() */
export type Booking = {
    name: string;
    starts_at: string;
    ends_at: string;
    invitee_name: string | null;
    invitee_email: string | null;
    join_url: string | null;
    location: string | null;
};

/** App\Domains\Bookings\Actions\GetUpcomingBookingsAction */
export type BookingsState = {
    configured: boolean;
    available: boolean;
    bookings: Booking[];
};

/** App\Domains\Dashboard\Actions\GetLeadStatsAction */
export type LeadStats = {
    unread: number;
    this_week: number;
    last_week: number;
    budgets: Array<{ label: string; count: number }>;
    project_types: Array<{ label: string; count: number }>;
};

/** App\Domains\Dashboard\Actions\GetContentHealthAction */
export type HealthCheck = {
    key: string;
    label: string;
    ok: boolean;
    detail: string;
    href: string;
};

export type AvailabilityControlProps = {
    status: AvailabilityStatus;
    note: string | null;
    options: SelectOption<AvailabilityStatus>[];
};

export type DashboardStats = {
    published_projects: number;
    draft_projects: number;
    published_posts: number;
    draft_posts: number;
    skills: number;
    unread_messages: number;
    total_messages: number;
};
