<?php

declare(strict_types=1);

namespace App\Domains\Contact\Controllers\Admin;

use App\Domains\Contact\Actions\DeleteContactMessageAction;
use App\Domains\Contact\Actions\MarkContactMessageAsReadAction;
use App\Domains\Contact\Actions\MarkContactMessageAsUnreadAction;
use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Contact\Queries\ContactMessageQueries;
use App\Domains\Contact\Resources\ContactMessageResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.ContactMessage::class, only: ['index']),
            new Middleware('can:view,message', only: ['show']),
            new Middleware('can:update,message', only: ['unread']),
            new Middleware('can:delete,message', only: ['destroy']),
        ];
    }

    public function index(Request $request, ContactMessageQueries $queries): Response
    {
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';

        return Inertia::render('admin/messages/index', [
            'messages' => $queries->paginate($filter),
            'filter' => $filter,
            'unreadCount' => $queries->unreadCount(),
        ]);
    }

    /**
     * Opening a message marks it as read.
     */
    public function show(ContactMessage $message, MarkContactMessageAsReadAction $markAsRead): Response
    {
        return Inertia::render('admin/messages/show', [
            'message' => ContactMessageResource::fromModel($markAsRead->execute($message)),
        ]);
    }

    public function unread(ContactMessage $message, MarkContactMessageAsUnreadAction $action): RedirectResponse
    {
        $action->execute($message);

        $this->toast('Marked as unread.');

        return to_route('admin.messages.index');
    }

    public function destroy(ContactMessage $message, DeleteContactMessageAction $action): RedirectResponse
    {
        $action->execute($message);

        $this->toast('Message deleted.');

        return to_route('admin.messages.index');
    }
}
