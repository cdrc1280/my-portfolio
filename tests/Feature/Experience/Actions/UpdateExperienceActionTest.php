<?php

use App\Domains\Experience\Actions\UpdateExperienceAction;
use App\Domains\Experience\Data\ExperienceData;
use App\Domains\Experience\Enums\EmploymentType;
use App\Domains\Experience\Models\Experience;
use Carbon\CarbonImmutable;

test('updates a role and can end it', function () {
    $experience = Experience::factory()->current()->create();

    app(UpdateExperienceAction::class)->execute($experience, new ExperienceData(
        company: 'Keen Eye',
        role: 'Front-end developer',
        employmentType: EmploymentType::Freelance,
        startedOn: CarbonImmutable::parse('2024-10-01'),
        endedOn: CarbonImmutable::parse('2025-03-01'),
    ));

    expect($experience->fresh())
        ->company->toBe('Keen Eye')
        ->employment_type->toBe(EmploymentType::Freelance)
        ->ended_on->format('Y-m')->toBe('2025-03');
});
