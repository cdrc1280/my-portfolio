import type { Tag } from '@/types';

/** App\Domain\Projects\Enums\ProjectStatus */
export type ProjectStatus = 'draft' | 'published';

/** App\Http\Resources\ProjectCardResource */
export type ProjectCard = {
    id: number;
    title: string;
    slug: string;
    summary: string;
    role: string | null;
    period: string | null;
    cover_url: string | null;
    is_featured: boolean;
    tags: Tag[];
};

export type ProjectImage = {
    id: number;
    url: string;
    alt: string;
};

/** App\Http\Resources\ProjectResource */
export type Project = {
    id: number;
    title: string;
    slug: string;
    summary: string;
    body_html: string | null;
    role: string | null;
    period: string | null;
    highlights: string[];
    cover_url: string | null;
    live_url: string | null;
    repo_url: string | null;
    published_at: string | null;
    tags: Tag[];
    images: ProjectImage[];
};

/** App\Http\Resources\Admin\ProjectResource */
export type AdminProject = {
    id: number;
    title: string;
    slug: string;
    summary: string;
    body: string | null;
    role: string | null;
    period: string | null;
    highlights: string[];
    cover_url: string | null;
    live_url: string | null;
    repo_url: string | null;
    status: ProjectStatus;
    is_featured: boolean;
    position: number;
    published_at: string | null;
    updated_at: string | null;
    tags: Tag[];
    /** Only present on the edit page. */
    images?: Array<Omit<ProjectImage, 'alt'> & { alt: string | null }>;
};
