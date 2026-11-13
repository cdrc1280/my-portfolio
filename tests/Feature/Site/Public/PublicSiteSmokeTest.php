<?php

use App\Domains\Blog\Models\Post;
use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Profile\Models\Profile;
use App\Domains\Projects\Models\Project;
use App\Domains\Services\Models\Service;
use App\Domains\Testimonials\Models\Testimonial;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Support\Facades\Storage;

/*
 * Loads the real portfolio content and requests every public page, case study and share card,
 * so a broken page, seeder or image fails CI before it reaches production.
 */
beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
    $this->seed(PortfolioSeeder::class);
});

test('every public page renders with the real content', function (string $route) {
    $this->get(route($route))->assertOk();
})->with(['home', 'about', 'projects.index', 'services', 'contact.show', 'sitemap', 'robots']);

test('every case study and its share card render', function () {
    $projects = Project::query()->published()->get();

    expect($projects)->not->toBeEmpty();

    foreach ($projects as $project) {
        $this->get(route('projects.show', $project->slug))->assertOk();
        $this->get(route('og.project', $project->slug))->assertOk()->assertHeader('Content-Type', 'image/png');
    }
});

test('the seeded content contains nothing made up', function () {
    expect(Testimonial::query()->count())->toBe(0)
        ->and(ContactMessage::query()->count())->toBe(0)
        ->and(Post::query()->count())->toBe(0)
        ->and(Service::query()->count())->toBe(4)
        ->and(Project::query()->whereNotNull('metrics')->count())->toBe(0)
        ->and(Profile::query()->sole())
        ->starting_rate->toBeNull()
        ->engagement_types->toBeNull()
        ->process_steps->toBeNull();
});
