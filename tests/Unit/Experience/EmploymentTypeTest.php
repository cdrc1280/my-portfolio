<?php

use App\Domains\Experience\Enums\EmploymentType;

test('every case has a human label', function (EmploymentType $type) {
    expect($type->label())->not->toBeEmpty();
})->with(EmploymentType::cases());

test('options mirror the cases', function () {
    expect(EmploymentType::options())->toHaveCount(count(EmploymentType::cases()))
        ->and(EmploymentType::options()[0])->toBe(['value' => 'full_time', 'label' => 'Full-time']);
});
