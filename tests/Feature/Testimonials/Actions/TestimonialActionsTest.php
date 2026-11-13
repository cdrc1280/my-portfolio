<?php

use App\Domains\Testimonials\Actions\CreateTestimonialAction;
use App\Domains\Testimonials\Actions\DeleteTestimonialAction;
use App\Domains\Testimonials\Actions\ReorderTestimonialsAction;
use App\Domains\Testimonials\Actions\UpdateTestimonialAction;
use App\Domains\Testimonials\Data\TestimonialData;
use App\Domains\Testimonials\Models\Testimonial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

test('create stores the photo and defaults to unpublished', function () {
    $testimonial = app(CreateTestimonialAction::class)->execute(TestimonialData::from([
        'author_name' => ' Maria ',
        'quote' => 'Great work.',
        'project_id' => '',
        'photo' => UploadedFile::fake()->image('maria.jpg'),
    ]));

    expect($testimonial)
        ->author_name->toBe('Maria')
        ->project_id->toBeNull()
        ->has_permission->toBeFalse()
        ->is_featured->toBeFalse();
    Storage::disk('public')->assertExists($testimonial->photo_path);
});

test('update replaces the photo and deletes the old one', function () {
    Storage::disk('public')->put('testimonials/old.jpg', 'x');
    $testimonial = Testimonial::factory()->create(['photo_path' => 'testimonials/old.jpg']);

    app(UpdateTestimonialAction::class)->execute($testimonial, TestimonialData::from([
        'author_name' => 'Maria', 'quote' => 'Great.', 'photo' => UploadedFile::fake()->image('new.jpg'),
    ]));

    expect($testimonial->fresh()->photo_path)->not->toBe('testimonials/old.jpg');
    Storage::disk('public')->assertMissing('testimonials/old.jpg');
});

test('update keeps the photo when none is given', function () {
    $testimonial = Testimonial::factory()->create(['photo_path' => 'testimonials/keep.jpg']);

    app(UpdateTestimonialAction::class)->execute($testimonial, TestimonialData::from(['author_name' => 'A', 'quote' => 'B']));

    expect($testimonial->fresh()->photo_path)->toBe('testimonials/keep.jpg');
});

test('delete removes the record and photo', function () {
    Storage::disk('public')->put('testimonials/a.jpg', 'x');
    $testimonial = Testimonial::factory()->create(['photo_path' => 'testimonials/a.jpg']);

    app(DeleteTestimonialAction::class)->execute($testimonial);

    $this->assertModelMissing($testimonial);
    Storage::disk('public')->assertMissing('testimonials/a.jpg');
});

test('reorder sets positions', function () {
    [$a, $b] = Testimonial::factory()->count(2)->create();

    app(ReorderTestimonialsAction::class)->execute([$b->id, $a->id]);

    expect($b->fresh()->position)->toBe(1);
});

test('only testimonials with permission are publishable', function () {
    Testimonial::factory()->create();
    $ok = Testimonial::factory()->publishable()->create();

    expect(Testimonial::query()->publishable()->pluck('id')->all())->toBe([$ok->id]);
});
