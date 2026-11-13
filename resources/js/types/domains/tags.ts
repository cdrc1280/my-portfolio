import type { TagCategory } from '@/types';

/** One group from App\Domains\Tags\Queries\SkillGroupsQuery. */
export type SkillGroup = {
    category: TagCategory;
    label: string;
    skills: Array<{ id: number; name: string; slug: string; icon: string | null }>;
};

/** App\Domains\Tags\Resources\AdminTagResource */
export type AdminTag = {
    id: number;
    name: string;
    slug: string;
    category: TagCategory | null;
    category_label: string | null;
    icon: string | null;
    position: number;
    projects_count: number;
    posts_count: number;
};
