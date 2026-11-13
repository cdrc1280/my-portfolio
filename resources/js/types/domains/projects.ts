import type { FolderNode } from '@/components/custom/folder-tree';
import type { Tag } from '@/types';

/** App\Domains\Projects\Enums\ProjectStatus */
export type ProjectStatus = 'draft' | 'published';

export type ProjectMetric = { label: string; value: string };

/** App\Domains\*\Resources\ProjectCardResource (dates are YYYY-MM) */
export type ProjectCard = {
    id: number;
    title: string;
    slug: string;
    summary: string;
    role: string | null;
    started_on: string | null;
    ended_on: string | null;
    cover_url: string | null;
    live_url: string | null;
    /** Always null for confidential projects. */
    repo_url: string | null;
    is_confidential: boolean;
    is_featured: boolean;
    tags: Tag[];
};

export type ProjectImage = {
    id: number;
    url: string;
    alt: string;
};

/** App\Domains\*\Resources\ProjectResource: long-form fields are server-rendered, sanitized HTML. */
export type Project = {
    id: number;
    title: string;
    slug: string;
    summary: string;
    role: string | null;
    started_on: string | null;
    ended_on: string | null;
    problem_html: string | null;
    role_html: string | null;
    architecture_html: string | null;
    architecture_tree: FolderNode[];
    diagram_url: string | null;
    decisions_html: string | null;
    challenges_html: string | null;
    results_html: string | null;
    metrics: ProjectMetric[];
    cover_url: string | null;
    live_url: string | null;
    repo_url: string | null;
    demo_url: string | null;
    is_confidential: boolean;
    published_at: string | null;
    seo_title: string | null;
    seo_description: string | null;
    tags: Tag[];
    images: ProjectImage[];
};

/** The `next` prop on the case-study page. */
export type NextProject = { title: string; slug: string; summary: string };

/** App\Domains\*\Resources\AdminProjectResource */
export type AdminProject = {
    id: number;
    title: string;
    slug: string;
    summary: string;
    problem: string | null;
    role: string | null;
    role_details: string | null;
    architecture: string | null;
    /** Indented text: folders end in "/", notes follow "#". */
    architecture_tree: string;
    diagram_url: string | null;
    decisions: string | null;
    challenges: string | null;
    results: string | null;
    metrics: ProjectMetric[];
    cover_url: string | null;
    live_url: string | null;
    repo_url: string | null;
    demo_url: string | null;
    is_confidential: boolean;
    started_on: string | null;
    ended_on: string | null;
    status: ProjectStatus;
    is_featured: boolean;
    seo_title: string | null;
    seo_description: string | null;
    position: number;
    published_at: string | null;
    updated_at: string | null;
    tags: Tag[];
    /** Only present on the edit page. */
    images?: Array<Omit<ProjectImage, 'alt'> & { alt: string | null }>;
};
