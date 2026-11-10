<?php

use App\Domain\Projects\Models\Project;
use App\Domain\Tags\Models\Tag;
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
    $unused = Tag::factory()->create(['slug' => 'unused']);
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

    expect($unused->exists)->toBeTrue();
});

test('show renders a published project with rendered markdown', function () {
    $project = Project::factory()->published()->create(['slug' => 'lms', 'body' => "## Approach\n\n<script>x</script>"]);

    $this->get(route('projects.show', 'lms'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/projects/show')
            ->where('project.id', $project->id)
            ->where('project.body_html', fn (string $html) => str_contains($html, '<h2>Approach</h2>') && ! str_contains($html, '<script>'))
            ->has('project.images')
            ->has('related'));
});

test('show lists related published projects sharing a tag', function () {
    $tag = Tag::factory()->create();
    $project = Project::factory()->published()->create();
    $related = Project::factory()->published()->create();
    $draft = Project::factory()->create();
    foreach ([$project, $related, $draft] as $p) {
        $p->tags()->attach($tag);
    }

    $this->get(route('projects.show', $project->slug))
        ->assertInertia(fn (Assert $page) => $page->has('related', 1)->where('related.0.id', $related->id));
});

test('draft and unknown projects are not found', function () {
    Project::factory()->create(['slug' => 'secret']);

    $this->get(route('projects.show', 'secret'))->assertNotFound();
    $this->get(route('projects.show', 'nope'))->assertNotFound();
});
