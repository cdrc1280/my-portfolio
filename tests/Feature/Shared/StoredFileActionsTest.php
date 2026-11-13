<?php

use App\Shared\Actions\DeleteStoredFileAction;
use App\Shared\Actions\StoreUploadedFileAction;
use App\Shared\Support\MediaUrl;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

test('stores an upload on the media disk', function () {
    $path = app(StoreUploadedFileAction::class)->execute(UploadedFile::fake()->image('cover.jpg'), 'projects');

    expect($path)->toStartWith('projects/');
    Storage::disk('public')->assertExists($path);
});

test('replacing an upload deletes the previous file', function () {
    $action = app(StoreUploadedFileAction::class);
    $old = $action->execute(UploadedFile::fake()->image('old.jpg'), 'projects');

    $new = $action->execute(UploadedFile::fake()->image('new.jpg'), 'projects', replacing: $old);

    Storage::disk('public')->assertMissing($old);
    Storage::disk('public')->assertExists($new);
});

test('deleting ignores absolute urls and empty paths', function (?string $path) {
    app(DeleteStoredFileAction::class)->execute($path);

    expect(true)->toBeTrue();
})->with([null, '', 'https://example.com/a.jpg', '/images/a.jpg']);

test('media url resolves stored paths and passes absolute ones through', function () {
    expect(MediaUrl::for(null))->toBeNull()
        ->and(MediaUrl::for('https://cdn.test/a.jpg'))->toBe('https://cdn.test/a.jpg')
        ->and(MediaUrl::for('/images/a.jpg'))->toBe('/images/a.jpg')
        ->and(MediaUrl::for('projects/a.jpg'))->toEndWith('projects/a.jpg');
});
