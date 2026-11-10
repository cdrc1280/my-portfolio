<?php

declare(strict_types=1);

namespace App\Domain\Contact\Actions;

use App\Domain\Contact\Models\ContactMessage;

final class MarkContactMessageAsUnreadAction
{
    public function execute(ContactMessage $message): ContactMessage
    {
        $message->update(['read_at' => null]);

        return $message;
    }
}
