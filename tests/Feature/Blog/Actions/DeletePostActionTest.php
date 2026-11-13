<?php

use App\Domains\Blog\Actions\DeletePostAction;
use App\Domains\Blog\Models\Post;
use App\Domains\Tags\Models\Tag;
use Illuminate\Support\Facades\Storage;

test('deletes the post, its tag links and cover', function () {
    Storage::fake('public');
    Storage::disk('public')->put('posts/c.jpg', 'x');
    $post = Post::factory()->create(['cover_image' => 'posts/c.jpg']);
    $post->tags()->attach(Tag::factory()->create());

    app(DeletePostAction::class)->execute($post);

    $this->assertModelMissing($post);
    $this->assertDatabaseCount('taggables', 0);
    Storage::disk('public')->assertMissing('posts/c.jpg');
});
