import type { Tag } from '@/types';

/** App\Domain\Blog\Enums\PostStatus */
export type PostStatus = 'draft' | 'published';

/** App\Http\Resources\PostCardResource */
export type PostCard = {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover_url: string | null;
    published_at: string | null;
    reading_minutes: number;
    tags: Tag[];
};

/** App\Http\Resources\PostResource */
export type Post = PostCard & {
    body_html: string | null;
    updated_at: string | null;
};

/** App\Http\Resources\Admin\PostResource */
export type AdminPost = {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    body: string;
    cover_url: string | null;
    status: PostStatus;
    is_scheduled: boolean;
    published_at: string | null;
    updated_at: string | null;
    tags: Tag[];
};
