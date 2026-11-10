<?php

use App\Domain\Profile\Actions\GetProfileAction;
use App\Domain\Profile\Actions\UpdateProfileAction;
use App\Domain\Profile\DataTransferObjects\ProfileData;
use App\Domain\Profile\Events\ProfileChanged;
use App\Domain\Profile\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

test('updates fields, stores uploads and announces the change', function () {
    Event::fake([ProfileChanged::class]);

    $profile = app(UpdateProfileAction::class)->execute(new ProfileData(
        name: 'Cedric James Leala',
        headline: 'Full-stack developer',
        avatar: UploadedFile::fake()->image('me.jpg'),
        resume: UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
    ));

    expect($profile->fresh())
        ->name->toBe('Cedric James Leala')
        ->resume_updated_at->not->toBeNull();
    Storage::disk('public')->assertExists([$profile->avatar_path, $profile->resume_path]);
    Event::assertDispatched(ProfileChanged::class);
});

test('removing files deletes them', function () {
    Storage::disk('public')->put('profile/a.jpg', 'x');
    Storage::disk('public')->put('resume/cv.pdf', 'x');
    Profile::factory()->create(['avatar_path' => 'profile/a.jpg', 'resume_path' => 'resume/cv.pdf']);

    $profile = app(UpdateProfileAction::class)->execute(new ProfileData(
        name: 'X', headline: 'Y', removeAvatar: true, removeResume: true,
    ));

    expect($profile->fresh())->avatar_path->toBeNull()->resume_path->toBeNull();
    Storage::disk('public')->assertMissing(['profile/a.jpg', 'resume/cv.pdf']);
});

test('the cached snapshot is refreshed after an update', function () {
    Profile::factory()->create(['name' => 'Old']);
    expect(app(GetProfileAction::class)->snapshot()->name)->toBe('Old');

    app(UpdateProfileAction::class)->execute(new ProfileData(name: 'New', headline: 'Dev'));

    expect(app(GetProfileAction::class)->snapshot()->name)->toBe('New');
});
