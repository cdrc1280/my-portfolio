<?php

declare(strict_types=1);

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

/**
 * Marks every unread brief as read (the bell's "Mark all as read"). Returns how many changed.
 */
final class MarkAllContactMessagesAsReadAction
{
    public function execute(): int
    {
        return DB::transaction(
            fn (): int => ContactMessage::query()->unread()->update(['read_at' => now()]),
        );
    }
}
