<?php

use App\Domains\Projects\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

test('the first release seeds the admin and the real content', function () {
    $this->artisan('app:release')->assertSuccessful();

    expect(User::query()->where('is_admin', true)->count())->toBe(1)
        ->and(Project::query()->count())->toBeGreaterThan(0);
});

test('later releases never seed again', function () {
    $this->artisan('app:release')->assertSuccessful();
    Project::query()->first()?->update(['title' => 'Edited in the admin']);

    $this->artisan('app:release')->assertSuccessful();

    expect(User::query()->count())->toBe(1)
        ->and(Project::query()->where('title', 'Edited in the admin')->exists())->toBeTrue();
});

test('an existing site is left alone', function () {
    User::factory()->create();

    $this->artisan('app:release')->assertSuccessful();

    expect(Project::query()->count())->toBe(0);
});
