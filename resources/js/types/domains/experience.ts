/** App\Domains\Experience\Enums\EmploymentType */
export type EmploymentType = 'full_time' | 'part_time' | 'contract' | 'freelance' | 'internship';

/** App\Domains\*\Resources\ExperienceResource (dates are YYYY-MM) */
export type Experience = {
    id: number;
    /** Public pages get "Confidential client" for confidential roles; admin gets the real name (may be null). */
    company: string | null;
    company_url: string | null;
    is_confidential: boolean;
    role: string;
    location: string | null;
    is_remote: boolean;
    /** Null when not stated. */
    employment_type: EmploymentType | null;
    employment_type_label: string | null;
    started_on: string;
    ended_on: string | null;
    is_current: boolean;
    summary: string | null;
    highlights: string[];
    technologies: string[];
};

/** App\Domains\*\Resources\EducationResource */
export type Education = {
    id: number;
    institution: string;
    qualification: string;
    field_of_study: string | null;
    started_on: string;
    ended_on: string | null;
    notes: string | null;
};

/** App\Domains\*\Resources\CertificateResource */
export type Certificate = {
    id: number;
    name: string;
    issuer: string | null;
    issued_on: string;
    credential_url: string | null;
    badge_url: string | null;
};
