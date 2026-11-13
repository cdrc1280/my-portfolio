/** Mirrors App\Domains\Account\Resources\UserResource. */
export type User = {
    id: number;
    name: string;
    email: string;
    is_admin: boolean;
    email_verified_at: string | null;
    two_factor_enabled: boolean;
    created_at: string | null;
    updated_at: string | null;
};

export type Auth = {
    user: User | null;
};

export type TwoFactorSetupData = {
    svg: string;
    url: string;
};

export type TwoFactorSecretKey = {
    secretKey: string;
};
