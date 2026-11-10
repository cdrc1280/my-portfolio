/** App\Domain\Experience\Enums\EmploymentType */
export type EmploymentType = 'full_time' | 'part_time' | 'contract' | 'freelance' | 'internship';

/** App\Http\Resources\ExperienceResource (dates are YYYY-MM) */
export type Experience = {
    id: number;
    company: string;
    company_url: string | null;
    role: string;
    location: string | null;
    employment_type: EmploymentType;
    employment_type_label: string;
    started_on: string;
    ended_on: string | null;
    is_current: boolean;
    summary: string | null;
    highlights: string[];
    technologies: string[];
};

/** App\Http\Resources\EducationResource */
export type Education = {
    id: number;
    institution: string;
    qualification: string;
    field_of_study: string | null;
    started_on: string;
    ended_on: string | null;
    notes: string | null;
};

/** App\Http\Resources\CertificateResource */
export type Certificate = {
    id: number;
    name: string;
    issuer: string;
    issued_on: string;
    credential_url: string | null;
};
