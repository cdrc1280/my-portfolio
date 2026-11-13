<?php

use App\Domains\Projects\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

test('a half-finished first setup is wiped and built again', function () {
    // SQLite can't wipe inside RefreshDatabase's transaction, so this test leaves it and rebuilds
    // a clean schema afterwards for the tests that follow.
    DB::commit();

    // An instance stopped mid-migration: the migration's changes are applied but not recorded.
    DB::table('migrations')->where('migration', '2026_11_12_000600_extend_contact_messages_and_posts')->delete();

    try {
        $this->artisan('app:release')->assertSuccessful();

        expect(User::query()->where('is_admin', true)->count())->toBe(1)
            ->and(Schema::hasColumn('contact_messages', 'ip_hash'))->toBeTrue();
    } finally {
        // Wiping reconnects, so the following tests must reuse this rebuilt connection.
        Artisan::call('migrate:fresh');
        RefreshDatabaseState::$inMemoryConnections[DB::getDefaultConnection()] = DB::connection()->getPdo();
        DB::beginTransaction();
    }
});

test('a failing migration never wipes a site that is set up', function () {
    User::factory()->create();
    DB::table('migrations')->where('migration', '2026_11_12_000600_extend_contact_messages_and_posts')->delete();

    $this->artisan('app:release')->assertFailed();

    expect(User::query()->count())->toBe(1);
});
