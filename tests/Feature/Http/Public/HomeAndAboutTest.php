<?php

use App\Domain\Blog\Models\Post;
use App\Domain\Experience\Models\Experience;
use App\Domain\Profile\Models\Profile;
use App\Domain\Projects\Models\Project;
use App\Domain\Skills\Models\Skill;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;

test('home shows featured published work, experience, skills and posts', function () {
    $featured = Project::factory()->featured()->create();
    Project::factory()->published()->create();
    Project::factory()->create(['is_featured' => true]);
    Experience::factory()->count(5)->create();
    Skill::factory()->create();
    Post::factory()->published()->create();

    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/home')
            ->has('featuredProjects', 1)
            ->where('featuredProjects.0.id', $featured->id)
            ->has('experiences', 4)
            ->has('skillCategories', 1)
            ->has('latestPosts', 1)
            ->missing('github'));
});

test('github activity is a deferred prop', function () {
    config(['portfolio.github.username' => 'octo']);
    Http::fake([
        'api.github.com/users/octo' => Http::response(['login' => 'octo', 'public_repos' => 1, 'followers' => 0]),
        'api.github.com/users/octo/repos*' => Http::response([]),
    ]);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->missing('github')
            ->loadDeferredProps(fn (Assert $reload) => $reload->where('github.username', 'octo')));
});

test('about shows the rendered bio and full history', function () {
    Profile::factory()->create(['bio' => '## Hello', 'phone' => '0938-000']);
    Experience::factory()->count(5)->create();

    $this->get(route('about'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/about')
            ->where('profile.bio_html', fn (string $html) => str_contains($html, '<h2>Hello</h2>'))
            ->missing('profile.phone')
            ->has('experiences', 5)
            ->has('education')
            ->has('certificates')
            ->has('skillCategories'));
});
