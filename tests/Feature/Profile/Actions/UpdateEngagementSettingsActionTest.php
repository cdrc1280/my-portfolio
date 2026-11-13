<?php

use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Profile\Actions\UpdateEngagementSettingsAction;
use App\Domains\Profile\Data\EngagementSettingsData;
use App\Domains\Profile\Models\Profile;

test('engagement settings update the profile and refresh the cached snapshot', function () {
    Profile::factory()->create();
    app(GetProfileAction::class)->snapshot();

    app(UpdateEngagementSettingsAction::class)->execute(new EngagementSettingsData(
        startingRate: '$40/hour',
        engagementTypes: [['title' => 'Retainer', 'description' => '']],
    ));

    expect(Profile::query()->sole())->starting_rate->toBe('$40/hour')->engagement_types->toHaveCount(1);
});
