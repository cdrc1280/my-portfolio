/** App\Domain\GitHub\DataTransferObjects\GitHubActivityData::toArray() */
export type GitHubActivity = {
    username: string;
    profile_url: string;
    public_repos: number;
    followers: number;
    total_stars: number;
    contributions_last_year: number | null;
    languages: Array<{ name: string; share: number }>;
    recent_repos: Array<{
        name: string;
        url: string;
        description: string | null;
        language: string | null;
        stars: number;
        pushed_at: string;
    }>;
};
