<?php

use App\Domain\Profile\Enums\SocialPlatform;
use App\Domain\Profile\Models\Profile;
use App\Domain\Profile\Models\SocialLink;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(fn () => Storage::fake('public'));

test('guests are redirected and non-admins forbidden', function () {
    $this->get(route('admin.profile.edit'))->assertRedirect(route('login'));

    $link = SocialLink::factory()->create();
    $this->actingAs(nonAdmin());
    $this->get(route('admin.profile.edit'))->assertForbidden();
    $this->put(route('admin.profile.update'), ['name' => 'X', 'headline' => 'Y'])->assertForbidden();
    $this->post(route('admin.social-links.store'), ['platform' => 'github', 'url' => 'https://github.com/x'])->assertForbidden();
    $this->delete(route('admin.social-links.destroy', $link))->assertForbidden();
});

test('edit page exposes profile, links and platforms', function () {
    Profile::factory()->create(['name' => 'Cedric']);
    SocialLink::factory()->create();

    $this->actingAs(admin())
        ->get(route('admin.profile.edit'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/profile/edit')
            ->where('profile.name', 'Cedric')
            ->has('socialLinks', 1)
            ->where('platforms', SocialPlatform::options()));
});

test('update saves the profile with a résumé', function () {
    $this->actingAs(admin())
        ->put(route('admin.profile.update'), [
            'name' => 'Cedric James Leala',
            'headline' => 'Full-stack developer',
            'email' => 'hello@example.com',
            'resume' => UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf'),
        ])
        ->assertRedirect(route('admin.profile.edit'));

    expect(Profile::query()->sole())->name->toBe('Cedric James Leala')->resume_path->not->toBeNull();
});

test('update validates', function (array $payload, string $error) {
    $this->actingAs(admin())
        ->put(route('admin.profile.update'), [...['name' => 'A', 'headline' => 'B'], ...$payload])
        ->assertSessionHasErrors($error);
})->with([
    'missing name' => [['name' => ''], 'name'],
    'bad email' => [['email' => 'nope'], 'email'],
    'résumé must be pdf' => [['resume' => UploadedFile::fake()->image('cv.png')], 'resume'],
    'résumé too large' => [['resume' => UploadedFile::fake()->create('cv.pdf', 6000, 'application/pdf')], 'resume'],
]);

test('social links CRUD and validation', function () {
    $this->actingAs(admin());

    $this->post(route('admin.social-links.store'), ['platform' => 'email', 'url' => 'me@example.com'])
        ->assertRedirect(route('admin.profile.edit'));
    $link = SocialLink::query()->sole();
    expect($link->url)->toBe('mailto:me@example.com');

    $this->post(route('admin.social-links.store'), ['platform' => 'github', 'url' => 'not a url'])->assertSessionHasErrors('url');
    $this->post(route('admin.social-links.store'), ['platform' => 'myspace', 'url' => 'https://x.test'])->assertSessionHasErrors('platform');

    $this->put(route('admin.social-links.update', $link), ['platform' => 'github', 'url' => 'https://github.com/me'])->assertRedirect();
    expect($link->fresh()->platform)->toBe(SocialPlatform::GitHub);

    $other = SocialLink::factory()->create();
    $this->post(route('admin.social-links.reorder'), ['ids' => [$other->id, $link->id]])->assertRedirect();
    expect($other->fresh()->position)->toBe(1);

    $this->delete(route('admin.social-links.destroy', $link))->assertRedirect();
    $this->assertModelMissing($link);
});
