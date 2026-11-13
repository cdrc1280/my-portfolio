<?php

declare(strict_types=1);

namespace App\Domains\Uses\Controllers\Admin;

use App\Domains\Uses\Actions\CreateUsesItemAction;
use App\Domains\Uses\Actions\DeleteUsesItemAction;
use App\Domains\Uses\Actions\ReorderUsesItemsAction;
use App\Domains\Uses\Actions\UpdateUsesItemAction;
use App\Domains\Uses\Data\UsesItemData;
use App\Domains\Uses\Models\UsesItem;
use App\Domains\Uses\Queries\UsesItemQueries;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class UsesItemController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.UsesItem::class, only: ['index']),
            new Middleware('can:create,'.UsesItem::class, only: ['store']),
            new Middleware('can:update,uses_item', only: ['update']),
            new Middleware('can:reorder,'.UsesItem::class, only: ['reorder']),
            new Middleware('can:delete,uses_item', only: ['destroy']),
        ];
    }

    public function index(UsesItemQueries $queries): Response
    {
        return Inertia::render('admin/uses/index', [
            'items' => $queries->ordered(),
        ]);
    }

    public function store(UsesItemData $data, CreateUsesItemAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Item added.');

        return to_route('admin.uses.index');
    }

    public function update(UsesItemData $data, UsesItem $usesItem, UpdateUsesItemAction $action): RedirectResponse
    {
        $action->execute($usesItem, $data);

        $this->toast('Item updated.');

        return to_route('admin.uses.index');
    }

    public function destroy(UsesItem $usesItem, DeleteUsesItemAction $action): RedirectResponse
    {
        $action->execute($usesItem);

        $this->toast('Item deleted.');

        return to_route('admin.uses.index');
    }

    public function reorder(ReorderData $data, ReorderUsesItemsAction $action): RedirectResponse
    {
        $action->execute($data->ids);

        $this->toast('Order saved.');

        return to_route('admin.uses.index');
    }
}
