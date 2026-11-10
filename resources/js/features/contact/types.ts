/** App\Http\Resources\Admin\ContactMessageResource */
export type ContactMessage = {
    id: number;
    name: string;
    email: string;
    subject: string | null;
    excerpt: string;
    message: string;
    is_read: boolean;
    read_at: string | null;
    created_at: string | null;
};

export type InboxFilter = 'all' | 'unread';
