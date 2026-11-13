<?php

declare(strict_types=1);

namespace App\Domains\Tags\Controllers\Admin;

use App\Domains\Tags\Actions\CreateTagAction;
use App\Domains\Tags\Actions\DeleteTagAction;
use App\Domains\Tags\Actions\ReorderTagsAction;
use App\Domains\Tags\Actions\UpdateTagAction;
use App\Domains\Tags\Data\TagData;
use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Models\Tag;
use App\Domains\Tags\Queries\TagQueries;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Reference module: every admin CRUD controller mirrors this one.
 */
class TagController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.Tag::class, only: ['index']),
            new Middleware('can:create,'.Tag::class, only: ['store']),
            new Middleware('can:update,tag', only: ['update']),
            new Middleware('can:reorder,'.Tag::class, only: ['reorder']),
            new Middleware('can:delete,tag', only: ['destroy']),
        ];
    }

    public function index(TagQueries $queries): Response
    {
        return Inertia::render('admin/tags/index', [
            'tags' => $queries->forAdmin(),
            'categories' => TagCategory::options(),
        ]);
    }

    public function store(TagData $data, CreateTagAction $action): RedirectResponse
    {
        $tag = $action->execute($data);

        $this->toast("Tag \"{$tag->name}\" created.");

        return to_route('admin.tags.index');
    }

    public function update(TagData $data, Tag $tag, UpdateTagAction $action): RedirectResponse
    {
        $action->execute($tag, $data);

        $this->toast('Tag updated.');

        return to_route('admin.tags.index');
    }

    public function reorder(ReorderData $data, ReorderTagsAction $action): RedirectResponse
    {
        $action->execute($data->ids);

        $this->toast('Order saved.');

        return to_route('admin.tags.index');
    }

    public function destroy(Tag $tag, DeleteTagAction $action): RedirectResponse
    {
        $action->execute($tag);

        $this->toast('Tag deleted.');

        return to_route('admin.tags.index');
    }
}
