<?php

use App\Domains\Blog\Enums\PostStatus;
use App\Domains\Blog\Models\Post;
use Inertia\Testing\AssertableInertia as Assert;

function postPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Hello world',
        'excerpt' => 'A first post.',
        'body' => 'Body text',
        'status' => 'published',
    ], $overrides);
}

test('guests are redirected and non-admins forbidden', function () {
    $post = Post::factory()->create();

    $this->get(route('admin.posts.index'))->assertRedirect(route('login'));

    $this->actingAs(nonAdmin());
    $this->get(route('admin.posts.index'))->assertForbidden();
    $this->post(route('admin.posts.store'), postPayload())->assertForbidden();
    $this->put(route('admin.posts.update', $post), postPayload())->assertForbidden();
    $this->delete(route('admin.posts.destroy', $post))->assertForbidden();
});

test('index lists drafts first and paginates', function () {
    Post::factory()->published()->create();
    $draft = Post::factory()->create();

    $this->actingAs(admin())
        ->get(route('admin.posts.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/posts/index')
            ->has('posts.data', 2)
            ->where('posts.data.0.id', $draft->id)
            ->has('posts.meta'));
});

test('store creates a post with a derived slug', function () {
    $this->actingAs(admin())->post(route('admin.posts.store'), postPayload())->assertRedirect();

    expect(Post::query()->sole())->slug->toBe('hello-world')->status->toBe(PostStatus::Published);
});

test('store validates', function (array $payload, string $error) {
    Post::factory()->create(['slug' => 'taken']);

    $this->actingAs(admin())->post(route('admin.posts.store'), postPayload($payload))->assertSessionHasErrors($error);
})->with([
    'missing title' => [['title' => ''], 'title'],
    'missing body' => [['body' => ''], 'body'],
    'bad status' => [['status' => 'archived'], 'status'],
    'bad date' => [['published_at' => 'yesterday-ish'], 'published_at'],
    'duplicate slug' => [['slug' => 'taken'], 'slug'],
]);

test('edit, update and destroy', function () {
    $post = Post::factory()->create();
    $this->actingAs(admin());

    $this->get(route('admin.posts.edit', $post))
        ->assertInertia(fn (Assert $page) => $page->component('admin/posts/edit')->where('post.body', $post->body)->has('statuses'));

    $this->put(route('admin.posts.update', $post), postPayload(['slug' => $post->slug, 'title' => 'Renamed']))
        ->assertRedirect(route('admin.posts.edit', $post));
    expect($post->fresh()->title)->toBe('Renamed');

    $this->delete(route('admin.posts.destroy', $post))->assertRedirect(route('admin.posts.index'));
    $this->assertModelMissing($post);
});
