<?php

use App\Domains\Experience\Actions\CreateExperienceAction;
use App\Domains\Experience\Data\ExperienceData;
use App\Domains\Experience\Models\Experience;

test('confidential experience may omit the company and is labelled for the public', function () {
    $data = ExperienceData::from([
        'company' => '', 'role' => 'Developer', 'employment_type' => 'contract',
        'started_on' => '2025-01', 'is_confidential' => true, 'is_remote' => true,
    ]);
    $experience = Experience::query()->create(CreateExperienceAction::columns($data));

    expect($experience)
        ->company->toBeNull()
        ->is_remote->toBeTrue()
        ->displayCompany()->toBe('Confidential client');
});
