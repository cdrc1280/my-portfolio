<?php

declare(strict_types=1);

namespace App\Domains\Contact\Listeners;

use App\Domains\Contact\Events\ContactMessageSubmitted;
use App\Domains\Contact\Notifications\ContactMessageReceived;
use App\Domains\Profile\Actions\GetProfileAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

/**
 * Confirms receipt to the sender, on the queue so the form responds instantly.
 */
final class SendContactAutoReply implements ShouldQueue
{
    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        private readonly GetProfileAction $getProfile,
    ) {}

    public function handle(ContactMessageSubmitted $event): void
    {
        $profile = $this->getProfile->execute();

        Notification::route('mail', [$event->message->email => $event->message->name])
            ->notify(new ContactMessageReceived($event->message, $profile->name, $profile->response_time));
    }
}
