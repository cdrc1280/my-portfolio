<?php

use App\Domains\Experience\Actions\CreateEducationAction;
use App\Domains\Experience\Actions\DeleteEducationAction;
use App\Domains\Experience\Actions\UpdateEducationAction;
use App\Domains\Experience\Data\EducationData;
use App\Domains\Experience\Models\Education;

test('creates, updates and deletes education', function () {
    $education = app(CreateEducationAction::class)->execute(EducationData::from([
        'institution' => 'ACLC',
        'qualification' => 'BS Information Systems',
        'started_on' => '2018-06',
        'ended_on' => '2022-06',
        'notes' => '  ',
    ]));

    expect($education)->notes->toBeNull()->started_on->format('Y-m')->toBe('2018-06');

    app(UpdateEducationAction::class)->execute($education, EducationData::from([
        'institution' => 'ACLC College',
        'qualification' => 'BS Information Systems',
        'started_on' => '2018-06',
    ]));
    expect($education->fresh())->institution->toBe('ACLC College')->ended_on->toBeNull();

    app(DeleteEducationAction::class)->execute($education);
    expect(Education::query()->count())->toBe(0);
});
