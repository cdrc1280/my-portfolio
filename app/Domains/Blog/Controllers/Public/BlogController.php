<?php

declare(strict_types=1);

namespace App\Domains\Blog\Controllers\Public;

use App\Domains\Blog\Queries\PostQueries;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request, PostQueries $queries): Response
    {
        $tag = $request->string('tag')->trim()->toString() ?: null;

        return Inertia::render('public/blog/index', [
            'posts' => $queries->paginatePublished($tag),
            'activeTag' => $tag,
        ]);
    }

    public function show(string $slug, PostQueries $queries): Response
    {
        return Inertia::render('public/blog/show', [
            'post' => $queries->findPublished($slug),
        ]);
    }
}
