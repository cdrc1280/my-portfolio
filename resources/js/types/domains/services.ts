import type { TitledItem } from '@/types';

/** App\Domains\*\Resources\ServiceResource */
export type Service = {
    id: number;
    title: string;
    description: string;
    icon: string | null;
};

/** The `engagement` prop on the Services admin page. */
export type EngagementSettings = {
    starting_rate: string | null;
    engagement_types: TitledItem[];
    process_steps: TitledItem[];
};
