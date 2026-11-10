<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Domain\Blog\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostCardResource;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $tag = $request->string('tag')->trim()->toString() ?: null;

        return Inertia::render('public/blog/index', [
            'posts' => PostCardResource::collection(
                Post::query()->published()->withTag($tag)->with('tags')->newestFirst()->paginate(10)->withQueryString(),
            ),
            'activeTag' => $tag,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = Post::query()->published()->where('slug', $slug)->with('tags')->firstOrFail();

        return Inertia::render('public/blog/show', [
            'post' => PostResource::make($post),
        ]);
    }
}
