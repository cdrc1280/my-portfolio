export type Skill = {
    id: number;
    name: string;
};

/** App\Http\Resources\SkillCategoryResource */
export type SkillCategory = {
    id: number;
    name: string;
    skills: Skill[];
};
