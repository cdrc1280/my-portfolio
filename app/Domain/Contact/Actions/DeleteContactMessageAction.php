<?php

declare(strict_types=1);

namespace App\Domain\Contact\Actions;

use App\Domain\Contact\Models\ContactMessage;

final class DeleteContactMessageAction
{
    public function execute(ContactMessage $message): void
    {
        $message->delete();
    }
}
