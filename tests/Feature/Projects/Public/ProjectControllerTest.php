<?php

use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Models\Tag;
use App\Domains\Testimonials\Models\Testimonial;
use Inertia\Testing\AssertableInertia as Assert;

test('index shows only published projects in order', function () {
    $second = Project::factory()->published()->create(['position' => 2]);
    $first = Project::factory()->published()->create(['position' => 1]);
    Project::factory()->create(['position' => 0]);

    $this->get(route('projects.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/projects/index')
            ->has('projects', 2)
            ->where('projects.0.id', $first->id)
            ->where('projects.1.id', $second->id)
            ->missing('projects.0.status')
            ->where('activeTag', null));
});

test('index filters by tag and only offers tags with published projects', function () {
    $laravel = Tag::factory()->create(['slug' => 'laravel']);
    Tag::factory()->create(['slug' => 'unused']);
    $draftOnly = Tag::factory()->create(['slug' => 'draft-only']);

    $tagged = Project::factory()->published()->create();
    $tagged->tags()->attach($laravel);
    Project::factory()->published()->create();
    Project::factory()->create()->tags()->attach($draftOnly);

    $this->get(route('projects.index', ['tag' => 'laravel']))
        ->assertInertia(fn (Assert $page) => $page
            ->has('projects', 1)
            ->where('projects.0.id', $tagged->id)
            ->has('tags', 1)
            ->where('tags.0.slug', 'laravel')
            ->where('activeTag', 'laravel'));
});

test('show renders the case study sections as safe html', function () {
    $project = Project::factory()->published()->create([
        'slug' => 'lms',
        'problem' => "Manual **reports**\n\n<script>x</script>",
        'decisions' => '## Why Filament',
        'metrics' => [['label' => 'Load time', 'value' => '-60%']],
        'architecture_tree' => [['name' => 'app', 'children' => [['name' => 'Domain']]]],
        'started_on' => '2024-01-01',
        'ended_on' => '2024-06-01',
    ]);

    $this->get(route('projects.show', 'lms'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/projects/show')
            ->where('project.id', $project->id)
            ->where('project.problem_html', fn (string $html) => str_contains($html, '<strong>reports</strong>') && ! str_contains($html, '<script>'))
            ->where('project.decisions_html', fn (string $html) => str_contains($html, '<h2>Why Filament</h2>'))
            ->where('project.metrics.0.value', '-60%')
            ->where('project.architecture_tree.0.children.0.name', 'Domain')
            ->where('project.started_on', '2024-01')
            ->where('project.ended_on', '2024-06')
            ->has('project.images')
            ->where('testimonial', null));
});

test('confidential projects never expose their repository', function () {
    Project::factory()->published()->confidential()->create(['slug' => 'secret-client']);

    $this->get(route('projects.show', 'secret-client'))
        ->assertInertia(fn (Assert $page) => $page
            ->where('project.is_confidential', true)
            ->where('project.repo_url', null));

    $this->get(route('projects.index'))
        ->assertInertia(fn (Assert $page) => $page->where('projects.0.repo_url', null));
});

test('show includes only a publishable related testimonial', function () {
    $project = Project::factory()->published()->create();
    Testimonial::factory()->for($project)->create(['quote' => 'Hidden']);
    Testimonial::factory()->publishable()->for($project)->create(['quote' => 'Great work']);

    $this->get(route('projects.show', $project->slug))
        ->assertInertia(fn (Assert $page) => $page->where('testimonial.quote', 'Great work'));
});

test('show links to the next published project and wraps around', function () {
    $first = Project::factory()->published()->create(['position' => 1]);
    $second = Project::factory()->published()->create(['position' => 2]);
    Project::factory()->create(['position' => 3]);

    $this->get(route('projects.show', $first->slug))
        ->assertInertia(fn (Assert $page) => $page->where('next.slug', $second->slug));

    $this->get(route('projects.show', $second->slug))
        ->assertInertia(fn (Assert $page) => $page->where('next.slug', $first->slug));
});

test('a lone project has no next link', function () {
    $only = Project::factory()->published()->create();

    $this->get(route('projects.show', $only->slug))->assertInertia(fn (Assert $page) => $page->where('next', null));
});

test('draft and unknown projects are not found', function () {
    Project::factory()->create(['slug' => 'secret']);

    $this->get(route('projects.show', 'secret'))->assertNotFound();
    $this->get(route('projects.show', 'nope'))->assertNotFound();
});
