<?php

declare(strict_types=1);

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Models\ContactMessage;

final class MarkContactMessageAsUnreadAction
{
    public function execute(ContactMessage $message): ContactMessage
    {
        $message->update(['read_at' => null]);

        return $message;
    }
}
