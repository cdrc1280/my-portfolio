<?php

declare(strict_types=1);

namespace App\Domains\Contact\Queries;

use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Contact\Resources\ContactMessageResource;
use App\Shared\Support\Paginated;

/**
 * Read side of the inbox.
 */
final class ContactMessageQueries
{
    /**
     * Newest first, 20 per page; `unread` limits the list to unread briefs.
     *
     * @param  'all'|'unread'  $filter
     * @return array<string, mixed>
     */
    public function paginate(string $filter): array
    {
        $messages = ContactMessage::query()
            ->when($filter === 'unread', fn ($query) => $query->unread())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Paginated::from($messages, fn (ContactMessage $message): ContactMessageResource => ContactMessageResource::fromModel($message));
    }

    public function unreadCount(): int
    {
        return ContactMessage::query()->unread()->count();
    }
}
