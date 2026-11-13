<?php

use App\Domains\Experience\Actions\CreateExperienceAction;
use App\Domains\Experience\Data\ExperienceData;
use App\Domains\Experience\Enums\EmploymentType;

test('creates a current role from validated input', function () {
    $experience = app(CreateExperienceAction::class)->execute(ExperienceData::from([
        'company' => 'TESDA',
        'role' => 'Full-stack developer',
        'employment_type' => 'full_time',
        'started_on' => '2023-07',
        'ended_on' => null,
        'highlights' => ['Shipped six systems', ''],
        'technologies' => ['Laravel', ' Vue.js '],
    ]));

    expect($experience->fresh())
        ->employment_type->toBe(EmploymentType::FullTime)
        ->started_on->format('Y-m-d')->toBe('2023-07-01')
        ->ended_on->toBeNull()
        ->highlights->toBe(['Shipped six systems'])
        ->technologies->toBe(['Laravel', 'Vue.js'])
        ->and($experience->isCurrent())->toBeTrue();
});
