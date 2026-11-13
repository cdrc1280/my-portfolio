<?php

declare(strict_types=1);

namespace App\Domains\Blog\Controllers\Admin;

use App\Domains\Blog\Actions\CreatePostAction;
use App\Domains\Blog\Actions\DeletePostAction;
use App\Domains\Blog\Actions\UpdatePostAction;
use App\Domains\Blog\Data\PostData;
use App\Domains\Blog\Enums\PostStatus;
use App\Domains\Blog\Models\Post;
use App\Domains\Blog\Queries\PostQueries;
use App\Domains\Blog\Resources\AdminPostResource;
use App\Domains\Tags\Queries\TagQueries;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.Post::class, only: ['index']),
            new Middleware('can:create,'.Post::class, only: ['create', 'store']),
            new Middleware('can:update,post', only: ['edit', 'update']),
            new Middleware('can:delete,post', only: ['destroy']),
        ];
    }

    public function index(PostQueries $queries): Response
    {
        return Inertia::render('admin/posts/index', [
            'posts' => $queries->paginateForAdmin(),
        ]);
    }

    public function create(TagQueries $tags): Response
    {
        return Inertia::render('admin/posts/create', $this->formOptions($tags));
    }

    public function store(PostData $data, CreatePostAction $action): RedirectResponse
    {
        $post = $action->execute($data);

        $this->toast("Post \"{$post->title}\" created.");

        return to_route('admin.posts.edit', $post);
    }

    public function edit(Post $post, TagQueries $tags): Response
    {
        return Inertia::render('admin/posts/edit', [
            'post' => AdminPostResource::fromModel($post->load('tags')),
            ...$this->formOptions($tags),
        ]);
    }

    public function update(PostData $data, Post $post, UpdatePostAction $action): RedirectResponse
    {
        $action->execute($post, $data);

        $this->toast('Post saved.');

        return to_route('admin.posts.edit', $post);
    }

    public function destroy(Post $post, DeletePostAction $action): RedirectResponse
    {
        $action->execute($post);

        $this->toast('Post deleted.');

        return to_route('admin.posts.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(TagQueries $tags): array
    {
        return [
            'tags' => $tags->allAsOptions(),
            'statuses' => PostStatus::options(),
        ];
    }
}
