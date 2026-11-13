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
