<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Contact\Actions\DeleteContactMessageAction;
use App\Domain\Contact\Actions\MarkContactMessageAsReadAction;
use App\Domain\Contact\Actions\MarkContactMessageAsUnreadAction;
use App\Domain\Contact\Models\ContactMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ContactMessageResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MessageController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', ContactMessage::class);

        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';

        $messages = ContactMessage::query()
            ->when($filter === 'unread', fn ($query) => $query->unread())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/messages/index', [
            'messages' => ContactMessageResource::collection($messages),
            'filter' => $filter,
            'unreadCount' => ContactMessage::query()->unread()->count(),
        ]);
    }

    /**
     * Opening a message marks it as read.
     */
    public function show(ContactMessage $message, MarkContactMessageAsReadAction $markAsRead): Response
    {
        $this->authorize('view', $message);

        return Inertia::render('admin/messages/show', [
            'message' => ContactMessageResource::make($markAsRead->execute($message)),
        ]);
    }

    public function unread(ContactMessage $message, MarkContactMessageAsUnreadAction $action): RedirectResponse
    {
        $this->authorize('update', $message);

        $action->execute($message);

        $this->toast('Marked as unread.');

        return to_route('admin.messages.index');
    }

    public function destroy(ContactMessage $message, DeleteContactMessageAction $action): RedirectResponse
    {
        $this->authorize('delete', $message);

        $action->execute($message);

        $this->toast('Message deleted.');

        return to_route('admin.messages.index');
    }
}
