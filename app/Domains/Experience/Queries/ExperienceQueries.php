<?php

declare(strict_types=1);

namespace App\Domains\Experience\Queries;

use App\Domains\Experience\Models\Certificate;
use App\Domains\Experience\Models\Education;
use App\Domains\Experience\Models\Experience;
use App\Domains\Experience\Resources\AdminExperienceResource;
use App\Domains\Experience\Resources\CertificateResource;
use App\Domains\Experience\Resources\EducationResource;
use App\Domains\Experience\Resources\ExperienceResource;

/**
 * Read side of the Experience module (work history, education, certificates).
 */
final class ExperienceQueries
{
    /**
     * Roles in manual order as shown publicly, optionally only the first few.
     *
     * @return list<ExperienceResource>
     */
    public function roles(?int $limit = null): array
    {
        return array_values(Experience::query()->ordered()->when($limit, fn ($query, int $limit) => $query->limit($limit))->get()
            ->map(fn (Experience $experience): ExperienceResource => ExperienceResource::fromModel($experience))
            ->all());
    }

    /**
     * @return list<ExperienceResource>
     */
    public function rolesForAdmin(): array
    {
        return array_values(Experience::query()->ordered()->get()
            ->map(fn (Experience $experience): ExperienceResource => AdminExperienceResource::fromModel($experience))
            ->all());
    }

    /**
     * @return list<EducationResource>
     */
    public function education(): array
    {
        return array_values(Education::query()->ordered()->get()
            ->map(fn (Education $education): EducationResource => EducationResource::fromModel($education))
            ->all());
    }

    /**
     * @return list<CertificateResource>
     */
    public function certificates(): array
    {
        return array_values(Certificate::query()->ordered()->get()
            ->map(fn (Certificate $certificate): CertificateResource => CertificateResource::fromModel($certificate))
            ->all());
    }
}
