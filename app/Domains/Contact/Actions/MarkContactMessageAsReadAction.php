<?php

declare(strict_types=1);

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Models\ContactMessage;

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
