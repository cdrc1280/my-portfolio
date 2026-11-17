<?php

declare(strict_types=1);

namespace App\Domains\Board\Controllers\Admin;

use App\Domains\Board\Actions\ClearDoneCardsAction;
use App\Domains\Board\Actions\CreateBoardCardAction;
use App\Domains\Board\Actions\DeleteBoardCardAction;
use App\Domains\Board\Actions\MoveBoardCardAction;
use App\Domains\Board\Actions\UpdateBoardCardAction;
use App\Domains\Board\Data\BoardCardData;
use App\Domains\Board\Data\MoveCardData;
use App\Domains\Board\Enums\CardPriority;
use App\Domains\Board\Enums\CardStatus;
use App\Domains\Board\Models\BoardCard;
use App\Domains\Board\Queries\BoardQueries;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The admin's private kanban board.
 */
class BoardCardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.BoardCard::class, only: ['index']),
            new Middleware('can:create,'.BoardCard::class, only: ['store']),
            new Middleware('can:update,card', only: ['update', 'move']),
            new Middleware('can:delete,card', only: ['destroy']),
            new Middleware('can:delete,'.BoardCard::class, only: ['clearDone']),
        ];
    }

    public function index(BoardQueries $queries): Response
    {
        return Inertia::render('admin/board/index', [
            'columns' => $queries->columns(),
            'statuses' => CardStatus::options(),
            'priorities' => CardPriority::options(),
        ]);
    }

    public function store(BoardCardData $data, CreateBoardCardAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Card added.');

        return to_route('admin.board.index');
    }

    public function update(BoardCardData $data, BoardCard $card, UpdateBoardCardAction $action): RedirectResponse
    {
        $action->execute($card, $data);

        $this->toast('Card updated.');

        return to_route('admin.board.index');
    }

    public function move(MoveCardData $data, BoardCard $card, MoveBoardCardAction $action): RedirectResponse
    {
        $action->execute($card, $data);

        return to_route('admin.board.index');
    }

    public function destroy(BoardCard $card, DeleteBoardCardAction $action): RedirectResponse
    {
        $action->execute($card);

        $this->toast('Card deleted.');

        return to_route('admin.board.index');
    }

    public function clearDone(ClearDoneCardsAction $action): RedirectResponse
    {
        $count = $action->execute();

        $this->toast($count === 1 ? '1 done card cleared.' : "{$count} done cards cleared.");

        return to_route('admin.board.index');
    }
}
