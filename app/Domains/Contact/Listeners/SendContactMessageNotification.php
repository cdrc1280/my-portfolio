<?php

declare(strict_types=1);

namespace App\Domains\Contact\Listeners;

use App\Domains\Contact\Events\ContactMessageSubmitted;
use App\Domains\Contact\Notifications\NewContactMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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

        try {
            Notification::route('mail', $recipient)->notify(new NewContactMessage($event->message));
        } catch (TransportExceptionInterface $e) {
            // A queue worker retries it. Without one (Vercel sends mail during the request), a mail
            // server problem is logged instead of failing the visitor's submission: the brief is saved.
            if (config('queue.default') !== 'sync') {
                throw $e;
            }

            report($e);
        }
    }
}
