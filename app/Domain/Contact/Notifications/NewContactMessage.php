<?php

declare(strict_types=1);

namespace App\Domain\Contact\Notifications;

use App\Domain\Contact\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

final class NewContactMessage extends Notification
{
    use Queueable;

    public function __construct(
        public readonly ContactMessage $message,
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
        $subject = $this->message->subject ?? Str::limit($this->message->message, 50);

        return (new MailMessage)
            ->subject("New message from {$this->message->name}: {$subject}")
            ->replyTo($this->message->email, $this->message->name)
            ->greeting('You have a new message')
            ->line("**From:** {$this->message->name} <{$this->message->email}>")
            ->line('**Message:**')
            ->line($this->message->message)
            ->action('Open in inbox', route('admin.messages.show', $this->message));
    }
}
