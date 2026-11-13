<?php

use App\Domains\Projects\Models\Project;
use App\Domains\Testimonials\Models\Testimonial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(fn () => Storage::fake('public'));

function testimonialPayload(array $overrides = []): array
{
    return array_merge([
        'author_name' => 'Maria Santos',
        'author_role' => 'CTO',
        'company' => 'Booking Co',
        'quote' => 'Shipped on time and the code was a pleasure to inherit.',
        'has_permission' => true,
        'is_featured' => true,
    ], $overrides);
}

test('guests are redirected and non-admins forbidden', function () {
    $testimonial = Testimonial::factory()->create();

    $this->get(route('admin.testimonials.index'))->assertRedirect(route('login'));

    $this->actingAs(nonAdmin());
    $this->get(route('admin.testimonials.index'))->assertForbidden();
    $this->post(route('admin.testimonials.store'), testimonialPayload())->assertForbidden();
    $this->put(route('admin.testimonials.update', $testimonial), testimonialPayload())->assertForbidden();
    $this->delete(route('admin.testimonials.destroy', $testimonial))->assertForbidden();
    $this->post(route('admin.testimonials.reorder'), ['ids' => [$testimonial->id]])->assertForbidden();
});

test('index lists testimonials, including unpublishable ones, with project options', function () {
    $project = Project::factory()->create(['title' => 'LMS']);
    Testimonial::factory()->create();
    Testimonial::factory()->publishable()->for($project)->create();

    $this->actingAs(admin())
        ->get(route('admin.testimonials.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/testimonials/index')
            ->has('testimonials', 2)
            ->where('testimonials.0.has_permission', false)
            ->where('testimonials.1.project.title', 'LMS')
            ->where('projects.0', ['value' => (string) $project->id, 'label' => 'LMS']));
});

test('store creates a testimonial with a photo and related project', function () {
    $project = Project::factory()->create();

    $this->actingAs(admin())
        ->post(route('admin.testimonials.store'), testimonialPayload([
            'project_id' => $project->id,
            'photo' => UploadedFile::fake()->image('maria.jpg'),
        ]))
        ->assertRedirect(route('admin.testimonials.index'));

    $testimonial = Testimonial::query()->sole();
    expect($testimonial)->project_id->toBe($project->id)->has_permission->toBeTrue();
    Storage::disk('public')->assertExists($testimonial->photo_path);
});

test('store validates input', function (array $payload, array $errors) {
    $this->actingAs(admin())
        ->post(route('admin.testimonials.store'), testimonialPayload($payload))
        ->assertSessionHasErrors($errors);
})->with([
    'missing author and quote' => [['author_name' => '', 'quote' => ''], ['author_name', 'quote']],
    'unknown project' => [['project_id' => 999], ['project_id']],
    'photo must be an image' => [['photo' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')], ['photo']],
]);

test('update removes the photo when asked', function () {
    Storage::disk('public')->put('testimonials/old.jpg', 'x');
    $testimonial = Testimonial::factory()->create(['photo_path' => 'testimonials/old.jpg']);

    $this->actingAs(admin())
        ->put(route('admin.testimonials.update', $testimonial), testimonialPayload(['remove_photo' => true]))
        ->assertRedirect();

    expect($testimonial->fresh()->photo_path)->toBeNull();
    Storage::disk('public')->assertMissing('testimonials/old.jpg');
});

test('destroy deletes the testimonial and its photo', function () {
    Storage::disk('public')->put('testimonials/old.jpg', 'x');
    $testimonial = Testimonial::factory()->create(['photo_path' => 'testimonials/old.jpg']);

    $this->actingAs(admin())->delete(route('admin.testimonials.destroy', $testimonial))->assertRedirect();

    $this->assertModelMissing($testimonial);
    Storage::disk('public')->assertMissing('testimonials/old.jpg');
});

test('reorder persists positions', function () {
    [$a, $b] = Testimonial::factory()->count(2)->create();

    $this->actingAs(admin())->post(route('admin.testimonials.reorder'), ['ids' => [$b->id, $a->id]])->assertRedirect();

    expect($b->fresh()->position)->toBe(1)->and($a->fresh()->position)->toBe(2);
});
