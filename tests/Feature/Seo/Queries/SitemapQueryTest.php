<?php

use App\Domains\Blog\Models\Post;
use App\Domains\Profile\Models\Profile;
use App\Domains\Projects\Models\Project;
use App\Domains\Seo\Queries\SitemapQuery;
use App\Domains\Services\Models\Service;
use App\Domains\Uses\Models\UsesItem;

test('includes static pages and only published content', function () {
    Project::factory()->published()->create(['slug' => 'live-project']);
    Project::factory()->create(['slug' => 'draft-project']);
    Post::factory()->published()->create(['slug' => 'live-post']);
    Post::factory()->scheduled()->create(['slug' => 'future-post']);

    $urls = array_map(fn ($entry) => $entry->url, app(SitemapQuery::class)->execute());

    expect($urls)
        ->toContain(route('home'), route('about'), route('projects.show', 'live-project'), route('blog.show', 'live-post'))
        ->not->toContain(route('projects.show', 'draft-project'), route('blog.show', 'future-post'));
});

test('includes services and uses only when they have content', function () {
    $urls = array_map(fn ($entry) => $entry->url, app(SitemapQuery::class)->execute());
    expect($urls)->toContain(route('contact.show'))->not->toContain(route('services'), route('uses'));

    Service::factory()->create();
    UsesItem::factory()->create();

    $urls = array_map(fn ($entry) => $entry->url, app(SitemapQuery::class)->execute());
    expect($urls)->toContain(route('services'), route('uses'));
});

test('leaves out the blog when it is switched off', function () {
    Profile::factory()->create(['blog_enabled' => false]);
    Post::factory()->published()->create(['slug' => 'live-post']);

    $urls = array_map(fn ($entry) => $entry->url, app(SitemapQuery::class)->execute());

    expect($urls)->not->toContain(route('blog.index'), route('blog.show', 'live-post'));
});
