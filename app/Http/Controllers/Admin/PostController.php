<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Blog\Actions\CreatePostAction;
use App\Domain\Blog\Actions\DeletePostAction;
use App\Domain\Blog\Actions\UpdatePostAction;
use App\Domain\Blog\Enums\PostStatus;
use App\Domain\Blog\Models\Post;
use App\Domain\Tags\Models\Tag;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\StorePostRequest;
use App\Http\Requests\Admin\Blog\UpdatePostRequest;
use App\Http\Resources\Admin\PostResource;
use App\Http\Resources\TagResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::query()
            ->with('tags')
            ->orderByRaw('published_at is null desc')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(20);

        return Inertia::render('admin/posts/index', [
            'posts' => PostResource::collection($posts),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Post::class);

        return Inertia::render('admin/posts/create', $this->formOptions());
    }

    public function store(StorePostRequest $request, CreatePostAction $action): RedirectResponse
    {
        $post = $action->execute($request->toData());

        $this->toast("Post \"{$post->title}\" created.");

        return to_route('admin.posts.edit', $post);
    }

    public function edit(Post $post): Response
    {
        $this->authorize('update', $post);

        return Inertia::render('admin/posts/edit', [
            'post' => PostResource::make($post->load('tags')),
            ...$this->formOptions(),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post, UpdatePostAction $action): RedirectResponse
    {
        $action->execute($post, $request->toData());

        $this->toast('Post saved.');

        return to_route('admin.posts.edit', $post);
    }

    public function destroy(Post $post, DeletePostAction $action): RedirectResponse
    {
        $this->authorize('delete', $post);

        $action->execute($post);

        $this->toast('Post deleted.');

        return to_route('admin.posts.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function formOptions(): array
    {
        return [
            'tags' => TagResource::collection(Tag::query()->orderBy('name')->get()),
            'statuses' => PostStatus::options(),
        ];
    }
}
