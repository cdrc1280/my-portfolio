<?php

use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Profile\Actions\UpdateAvailabilityAction;
use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Domains\Profile\Models\Profile;

test('availability updates refresh the shared snapshot', function () {
    Profile::factory()->create();
    app(GetProfileAction::class)->snapshot();

    app(UpdateAvailabilityAction::class)->execute(AvailabilityStatus::Unavailable, 'Booked until March');

    expect(app(GetProfileAction::class)->snapshot()->availabilityStatus)->toBe(AvailabilityStatus::Unavailable);
});
