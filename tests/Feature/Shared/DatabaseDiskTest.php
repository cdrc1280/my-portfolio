<?php

use App\Domains\Profile\Models\Profile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config(['filesystems.media_disk' => 'database']);
});

test('stores, reads, lists and deletes files in the database', function () {
    $disk = Storage::disk('database');

    $disk->put('resume/cv.pdf', '%PDF-1.4 fake');
    $disk->put('projects/a.png', 'png-bytes');
    $disk->put('projects/nested/b.png', 'more');

    expect($disk->exists('resume/cv.pdf'))->toBeTrue()
        ->and($disk->get('resume/cv.pdf'))->toBe('%PDF-1.4 fake')
        ->and($disk->size('projects/a.png'))->toBe(9)
        ->and($disk->files('projects'))->toBe(['projects/a.png'])
        ->and($disk->allFiles('projects'))->toBe(['projects/a.png', 'projects/nested/b.png'])
        ->and($disk->directories('projects'))->toBe(['projects/nested'])
        ->and($disk->url('resume/cv.pdf'))->toEndWith('/media/resume/cv.pdf');

    $disk->put('resume/cv.pdf', 'replaced');
    expect($disk->get('resume/cv.pdf'))->toBe('replaced');

    $disk->move('projects/a.png', 'projects/c.png');
    expect($disk->exists('projects/a.png'))->toBeFalse()->and($disk->get('projects/c.png'))->toBe('png-bytes');

    $disk->delete('resume/cv.pdf');
    $disk->deleteDirectory('projects');
    expect($disk->allFiles())->toBe([]);
});

test('keeps binary files intact', function () {
    $bytes = random_bytes(2048);
    Storage::disk('database')->put('bin/blob.dat', $bytes);

    expect(Storage::disk('database')->get('bin/blob.dat'))->toBe($bytes);
});

test('the media route serves stored files with long caching', function () {
    $png = UploadedFile::fake()->image('cover.png', 10, 10);
    $path = Storage::disk('database')->putFile('projects', $png);

    $this->get('/media/'.$path)
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png')
        ->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');

    $this->get('/media/projects/missing.png')->assertNotFound();
});

test('uploads made through settings land on the database disk and get a /media url', function () {
    Profile::factory()->create();

    $this->actingAs(admin())->put(route('admin.profile.update'), [
        'name' => 'Ada', 'headline' => 'Developer', 'availability_status' => 'available',
        'avatar' => UploadedFile::fake()->image('me.jpg'),
    ])->assertSessionHasNoErrors();

    $avatar = Profile::query()->sole()->avatar_path;

    expect(Storage::disk('database')->exists($avatar))->toBeTrue();
    $this->get('/media/'.$avatar)->assertOk()->assertHeader('Content-Type', 'image/jpeg');
});

test('the résumé downloads from the database disk', function () {
    Storage::disk('database')->put('resume/cv.pdf', '%PDF-1.4 fake');
    Profile::factory()->create(['resume_path' => 'resume/cv.pdf']);

    $response = $this->get(route('resume.download'))->assertOk();

    expect($response->streamedContent())->toBe('%PDF-1.4 fake');
});
