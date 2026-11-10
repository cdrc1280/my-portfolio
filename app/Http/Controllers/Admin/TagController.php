<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Tags\Actions\CreateTagAction;
use App\Domain\Tags\Actions\DeleteTagAction;
use App\Domain\Tags\Actions\UpdateTagAction;
use App\Domain\Tags\Models\Tag;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tags\StoreTagRequest;
use App\Http\Requests\Admin\Tags\UpdateTagRequest;
use App\Http\Resources\Admin\TagResource;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Tag::class);

        return Inertia::render('admin/tags/index', [
            'tags' => TagResource::collection(
                Tag::query()->withCount('projects')->orderBy('name')->get(),
            ),
        ]);
    }

    public function store(StoreTagRequest $request, CreateTagAction $action): RedirectResponse
    {
        $tag = $action->execute($request->toData());

        $this->toast("Tag \"{$tag->name}\" created.");

        return to_route('admin.tags.index');
    }

    public function update(UpdateTagRequest $request, Tag $tag, UpdateTagAction $action): RedirectResponse
    {
        $action->execute($tag, $request->toData());

        $this->toast('Tag updated.');

        return to_route('admin.tags.index');
    }

    public function destroy(Tag $tag, DeleteTagAction $action): RedirectResponse
    {
        $this->authorize('delete', $tag);

        $action->execute($tag);

        $this->toast('Tag deleted.');

        return to_route('admin.tags.index');
    }
}
