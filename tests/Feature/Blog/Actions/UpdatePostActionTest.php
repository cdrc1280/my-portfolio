<?php

use App\Domains\Blog\Actions\UpdatePostAction;
use App\Domains\Blog\Data\PostData;
use App\Domains\Blog\Enums\PostStatus;
use App\Domains\Blog\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('keeps the original publish date and replaces the cover', function () {
    Storage::fake('public');
    Storage::disk('public')->put('posts/old.jpg', 'x');
    $post = Post::factory()->published()->create(['cover_image' => 'posts/old.jpg', 'published_at' => now()->subMonth()]);
    $original = $post->published_at->toIso8601String();

    app(UpdatePostAction::class)->execute($post, new PostData(
        title: 'Edited',
        slug: $post->slug,
        excerpt: $post->excerpt,
        body: $post->body,
        status: PostStatus::Published,
        cover: UploadedFile::fake()->image('new.jpg'),
    ));

    $post->refresh();
    expect($post->title)->toBe('Edited')->and($post->published_at->toIso8601String())->toBe($original);
    Storage::disk('public')->assertMissing('posts/old.jpg');
    Storage::disk('public')->assertExists($post->cover_image);
});

test('unpublishing clears the date', function () {
    $post = Post::factory()->published()->create();

    app(UpdatePostAction::class)->execute($post, new PostData($post->title, $post->slug, $post->excerpt, $post->body));

    expect($post->fresh()->published_at)->toBeNull();
});
