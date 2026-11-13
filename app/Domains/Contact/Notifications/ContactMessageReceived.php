<?php

declare(strict_types=1);

namespace App\Domains\Contact\Notifications;

use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Auto-reply to the sender confirming their message arrived and when to expect an answer.
 */
final class ContactMessageReceived extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ContactMessage $message,
        public readonly string $ownerName,
        public readonly ?string $responseTime = null,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Thanks for your message, {$this->message->name}")
            ->greeting("Hi {$this->message->name},")
            ->line('Thanks for getting in touch. Your message arrived safely and I read every one personally.');

        if ($this->responseTime !== null) {
            $mail->line("You can expect a reply {$this->responseTime}.");
        }

        return $mail
            ->line('For reference, here is what you sent:')
            ->line('> '.str_replace("\n", "\n> ", $this->message->message))
            ->salutation("Best,\n{$this->ownerName}");
    }
}
