<?php

declare(strict_types=1);

namespace App\Domains\Contact\Events;

use App\Domains\Contact\Models\ContactMessage;
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
