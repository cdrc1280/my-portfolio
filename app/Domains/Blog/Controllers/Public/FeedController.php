<?php

declare(strict_types=1);

namespace App\Domains\Blog\Controllers\Public;

use App\Domains\Blog\Models\Post;
use App\Domains\Profile\Actions\GetProfileAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * RSS 2.0 feed of the 20 newest published posts. Behind EnsureBlogIsEnabled, like the blog itself.
 */
class FeedController extends Controller
{
    public function __invoke(GetProfileAction $getProfile): Response
    {
        $posts = Post::query()->published()->with('tags')->newestFirst()->limit(20)->get();

        return response()
            ->view('seo.feed', ['profile' => $getProfile->execute(), 'posts' => $posts])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
