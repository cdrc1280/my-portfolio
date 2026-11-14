/** App\Domains\*\Resources\AdminContactMessageResource (enum fields arrive as labels) */
export type ContactMessage = {
    id: number;
    name: string;
    email: string;
    project_type: string | null;
    budget: string | null;
    timeline: string | null;
    excerpt: string;
    message: string;
    is_read: boolean;
    read_at: string | null;
    created_at: string | null;
};

export type InboxFilter = 'all' | 'unread';

/** App\Domains\Contact\Resources\ContactNotificationResource: an unread brief in the admin bell. */
export type ContactNotification = {
    id: number;
    name: string;
    project_type: string | null;
    excerpt: string;
    created_at: string | null;
    received_ago: string | null;
};

/** Shared `notifications` prop (ContactMessageQueries::notifications()); null unless the user is the admin. */
export type ContactNotifications = {
    unread_count: number;
    items: ContactNotification[];
};
