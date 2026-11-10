<?php

declare(strict_types=1);

namespace App\Domain\Contact\Listeners;

use App\Domain\Contact\Events\ContactMessageSubmitted;
use App\Domain\Contact\Notifications\NewContactMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

/**
 * Emails the site owner about a new message, on the queue so the visitor
 * never waits on SMTP.
 */
final class SendContactMessageNotification implements ShouldQueue
{
    public int $tries = 3;

    public int $backoff = 30;

    public function handle(ContactMessageSubmitted $event): void
    {
        $recipient = config('portfolio.contact.notify_email');

        if (! is_string($recipient) || $recipient === '') {
            return;
        }

        Notification::route('mail', $recipient)->notify(new NewContactMessage($event->message));
    }
}
