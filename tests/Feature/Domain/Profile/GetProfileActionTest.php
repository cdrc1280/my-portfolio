<?php

use App\Domain\Profile\Actions\GetProfileAction;
use App\Domain\Profile\Enums\SocialPlatform;
use App\Domain\Profile\Models\Profile;
use App\Domain\Profile\Models\SocialLink;

test('creates a placeholder profile on a fresh install', function () {
    $profile = app(GetProfileAction::class)->execute();

    expect($profile->exists)->toBeTrue()
        ->and(Profile::query()->count())->toBe(1);

    app(GetProfileAction::class)->execute();
    expect(Profile::query()->count())->toBe(1);
});

test('snapshot contains ordered socials and no private fields', function () {
    Profile::factory()->create(['name' => 'Cedric', 'phone' => '0938']);
    SocialLink::factory()->create(['platform' => SocialPlatform::LinkedIn, 'position' => 2]);
    SocialLink::factory()->create(['platform' => SocialPlatform::GitHub, 'position' => 1]);

    $site = app(GetProfileAction::class)->snapshot()->toArray();

    expect($site)
        ->name->toBe('Cedric')
        ->resume_url->toBeNull()
        ->not->toHaveKey('phone')
        ->and(array_column($site['socials'], 'platform'))->toBe(['github', 'linkedin']);
});
