<?php

use App\Domains\Blog\Models\Post;
use App\Domains\Tags\Models\Tag;
use Inertia\Testing\AssertableInertia as Assert;

test('index lists only live posts, newest first', function () {
    $older = Post::factory()->published()->create(['published_at' => now()->subMonth()]);
    $newer = Post::factory()->published()->create(['published_at' => now()->subDay()]);
    Post::factory()->create();
    Post::factory()->scheduled()->create();

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/blog/index')
            ->has('posts.data', 2)
            ->where('posts.data.0.id', $newer->id)
            ->where('posts.data.1.id', $older->id)
            ->has('posts.data.0.reading_minutes')
            ->missing('posts.data.0.body_html'));
});

test('index filters by tag', function () {
    $tag = Tag::factory()->create(['slug' => 'laravel']);
    $tagged = Post::factory()->published()->create();
    $tagged->tags()->attach($tag);
    Post::factory()->published()->create();

    $this->get(route('blog.index', ['tag' => 'laravel']))
        ->assertInertia(fn (Assert $page) => $page->has('posts.data', 1)->where('activeTag', 'laravel'));
});

test('show renders markdown for a live post', function () {
    Post::factory()->published()->create(['slug' => 'hello', 'body' => '# Hi']);

    $this->get(route('blog.show', 'hello'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/blog/show')
            ->where('post.body_html', fn (string $html) => str_contains($html, '<h1>Hi</h1>')));
});

test('drafts and scheduled posts are not found', function () {
    Post::factory()->create(['slug' => 'draft']);
    Post::factory()->scheduled()->create(['slug' => 'future']);

    $this->get(route('blog.show', 'draft'))->assertNotFound();
    $this->get(route('blog.show', 'future'))->assertNotFound();
});
