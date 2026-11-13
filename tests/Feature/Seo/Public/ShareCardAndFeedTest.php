<?php

use App\Domains\Blog\Models\Post;
use App\Domains\Profile\Models\Profile;
use App\Domains\Projects\Models\Project;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

function assertPng($response): void
{
    $response->assertOk()->assertHeader('Content-Type', 'image/png');
    $size = getimagesizefromstring((string) file_get_contents($response->baseResponse->getFile()->getPathname()));

    expect($size)->not->toBeFalse()
        ->and([$size[0], $size[1]])->toBe([1200, 630]);
}

test('known pages get a generated 1200×630 share card', function (string $page) {
    Profile::factory()->create();

    assertPng($this->get(route('og.page', $page)));
})->with(['home', 'about', 'projects', 'contact']);

test('unknown pages have no share card', function () {
    $this->get(route('og.page', 'anything-else'))->assertNotFound();
});

test('only published projects get a share card', function () {
    $live = Project::factory()->published()->create(['title' => 'A very long project title that has to wrap across several lines of the card']);
    $draft = Project::factory()->create();

    assertPng($this->get(route('og.project', $live->slug)));
    $this->get(route('og.project', $draft->slug))->assertNotFound();
});

test('a card is rendered once and then served from disk', function () {
    $project = Project::factory()->published()->create();

    $this->get(route('og.project', $project->slug))->assertOk();
    $this->get(route('og.project', $project->slug))->assertOk();

    expect(Storage::disk('local')->files('og'))->toHaveCount(1);
});

test('post cards and the feed follow the blog switch', function () {
    $post = Post::factory()->published()->create();

    Profile::factory()->create(['blog_enabled' => false]);
    $this->get(route('og.post', $post->slug))->assertNotFound();
    $this->get(route('blog.feed'))->assertNotFound();
});

test('the feed lists published posts only, newest first', function () {
    Profile::factory()->create(['blog_enabled' => true]);
    Post::factory()->published()->create(['title' => 'Older', 'published_at' => now()->subWeek()]);
    Post::factory()->published()->create(['title' => 'Newer & better', 'published_at' => now()->subDay()]);
    Post::factory()->scheduled()->create(['title' => 'Future']);

    $response = $this->get(route('blog.feed'))->assertOk()->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8');
    $xml = simplexml_load_string($response->getContent());

    expect($xml)->not->toBeFalse()
        ->and(array_map('strval', $xml->xpath('//item/title')))->toBe(['Newer & better', 'Older']);
});
