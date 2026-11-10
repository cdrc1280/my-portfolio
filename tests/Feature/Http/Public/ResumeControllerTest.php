<?php

use App\Domain\Profile\Models\Profile;
use Illuminate\Support\Facades\Storage;

test('downloads the uploaded résumé with a friendly filename', function () {
    Storage::fake('public');
    Storage::disk('public')->put('resume/cv.pdf', '%PDF-1.4');
    Profile::factory()->create(['name' => 'Cedric James Leala', 'resume_path' => 'resume/cv.pdf']);

    $this->get(route('resume.download'))
        ->assertOk()
        ->assertDownload('cedric-james-leala-resume.pdf');
});

test('returns 404 when there is no résumé', function () {
    Profile::factory()->create(['resume_path' => null]);

    $this->get(route('resume.download'))->assertNotFound();
});

test('the shared site prop is present on public pages', function () {
    Profile::factory()->create(['name' => 'Cedric']);

    $this->get(route('projects.index'))
        ->assertInertia(fn ($page) => $page->where('site.name', 'Cedric')->has('site.socials'));
});
