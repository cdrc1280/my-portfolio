<?php

declare(strict_types=1);

namespace App\Domain\Contact\Events;

use App\Domain\Contact\Models\ContactMessage;
use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ContactMessageSubmitted implements ShouldDispatchAfterCommit
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly ContactMessage $message,
    ) {}
}
