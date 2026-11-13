<?php

use App\Domains\Blog\Actions\CreatePostAction;
use App\Domains\Blog\Data\PostData;
use App\Domains\Blog\Enums\PostStatus;
use App\Domains\Tags\Models\Tag;
use Carbon\CarbonImmutable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

function postData(array $overrides = []): PostData
{
    return new PostData(...array_merge([
        'title' => 'Shipping with Laravel',
        'slug' => 'shipping-with-laravel',
        'excerpt' => 'How I ship.',
        'body' => '## Hello',
    ], $overrides));
}

test('creates a draft without a publish date', function () {
    $post = app(CreatePostAction::class)->execute(postData());

    expect($post)->status->toBe(PostStatus::Draft)->published_at->toBeNull();
});

test('publishing stamps now unless a date is given', function () {
    $now = app(CreatePostAction::class)->execute(postData(['status' => PostStatus::Published]));
    $scheduled = app(CreatePostAction::class)->execute(postData([
        'slug' => 'later',
        'status' => PostStatus::Published,
        'publishedAt' => CarbonImmutable::now()->addWeek(),
    ]));

    expect($now->published_at)->not->toBeNull()
        ->and($scheduled->published_at->isFuture())->toBeTrue();
});

test('stores the cover and syncs tags', function () {
    $tag = Tag::factory()->create();

    $post = app(CreatePostAction::class)->execute(postData([
        'tagIds' => [$tag->id],
        'cover' => UploadedFile::fake()->image('c.jpg'),
    ]));

    Storage::disk('public')->assertExists($post->cover_image);
    expect($post->tags->modelKeys())->toBe([$tag->id]);
});
