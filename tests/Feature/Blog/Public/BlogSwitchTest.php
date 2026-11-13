<?php

use App\Domains\Blog\Models\Post;
use App\Domains\Profile\Models\Profile;

test('the blog returns 404 when switched off in settings', function () {
    Profile::factory()->create(['blog_enabled' => false]);
    $post = Post::factory()->published()->create();

    $this->get(route('blog.index'))->assertNotFound();
    $this->get(route('blog.show', $post->slug))->assertNotFound();
});

test('the blog is reachable when enabled', function () {
    Profile::factory()->create(['blog_enabled' => true]);

    $this->get(route('blog.index'))->assertOk();
});
