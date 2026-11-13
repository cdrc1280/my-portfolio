import type { Tag } from '@/types';

/** App\Domains\Blog\Enums\PostStatus */
export type PostStatus = 'draft' | 'published';

/** App\Domains\*\Resources\PostCardResource */
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

/** App\Domains\*\Resources\PostResource */
export type Post = PostCard & {
    body_html: string | null;
    seo_title: string | null;
    seo_description: string | null;
    updated_at: string | null;
};

/** App\Domains\*\Resources\AdminPostResource */
export type AdminPost = {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    seo_title: string | null;
    seo_description: string | null;
    body: string;
    cover_url: string | null;
    status: PostStatus;
    is_scheduled: boolean;
    published_at: string | null;
    updated_at: string | null;
    tags: Tag[];
};
