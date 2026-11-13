<?php

use App\Domains\Experience\Models\Experience;
use App\Domains\Profile\Models\Profile;
use App\Domains\Projects\Models\Project;
use App\Domains\Services\Models\Service;
use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Models\Tag;
use App\Domains\Testimonials\Models\Testimonial;
use App\Domains\WorkApproach\Models\WorkApproach;
use Inertia\Testing\AssertableInertia as Assert;

test('home shows featured work, approaches, services, skills, experience and testimonials', function () {
    $featured = Project::factory()->featured()->count(4)->create();
    Project::factory()->published()->create();
    Project::factory()->create(['is_featured' => true]);
    WorkApproach::factory()->count(5)->create();
    Service::factory()->count(5)->create();
    Experience::factory()->count(5)->create();
    Tag::factory()->skill(TagCategory::Frontend)->create(['name' => 'React']);
    Tag::factory()->skill(TagCategory::Backend)->create(['name' => 'Laravel']);
    Tag::factory()->create(['name' => 'Uncategorized']);
    Testimonial::factory()->featured()->create(['quote' => 'Shown']);
    Testimonial::factory()->publishable()->create();
    Testimonial::factory()->create(['is_featured' => true, 'quote' => 'No permission']);

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/home')
            ->has('featuredProjects', 3)
            ->where('featuredProjects.0.id', $featured->first()->id)
            ->has('approaches', 5)
            ->has('services', 4)
            ->has('experiences', 3)
            ->has('skillGroups', 2)
            ->where('skillGroups.0.category', 'backend')
            ->where('skillGroups.0.skills.0.name', 'Laravel')
            ->where('skillGroups.1.label', 'Frontend')
            ->has('testimonials', 1)
            ->where('testimonials.0.quote', 'Shown')
            ->missing('github'));
});

test('every page shares the site settings', function () {
    Profile::factory()->create([
        'availability_status' => 'limited',
        'availability' => 'Two slots left',
        'timezone' => 'UTC+8',
        'blog_enabled' => false,
        'seo_title' => 'Default title',
    ]);

    $this->get(route('home'))->assertInertia(fn (Assert $page) => $page
        ->where('site.availability.status', 'limited')
        ->where('site.availability.label', 'Limited availability')
        ->where('site.availability.note', 'Two slots left')
        ->where('site.timezone', 'UTC+8')
        ->where('site.blog_enabled', false)
        ->where('site.seo.title', 'Default title'));
});
