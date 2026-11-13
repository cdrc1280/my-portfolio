<?php

declare(strict_types=1);

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Models\ContactMessage;

final class DeleteContactMessageAction
{
    public function execute(ContactMessage $message): void
    {
        $message->delete();
    }
}
