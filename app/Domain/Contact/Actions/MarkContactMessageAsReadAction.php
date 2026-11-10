<?php

declare(strict_types=1);

namespace App\Domain\Contact\Actions;

use App\Domain\Contact\Models\ContactMessage;

final class MarkContactMessageAsReadAction
{
    public function execute(ContactMessage $message): ContactMessage
    {
        if (! $message->isRead()) {
            $message->update(['read_at' => now()]);
        }

        return $message;
    }
}
