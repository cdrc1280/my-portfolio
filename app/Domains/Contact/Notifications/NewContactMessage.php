<?php

declare(strict_types=1);

namespace App\Domains\Contact\Notifications;

use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells the site owner about a new brief. Sent from a queued listener.
 */
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
        $type = $this->message->project_type?->label() ?? 'General enquiry';

        return (new MailMessage)
            ->subject("New brief from {$this->message->name}: {$type}")
            ->replyTo($this->message->email, $this->message->name)
            ->greeting('You have a new project brief')
            ->line("**From:** {$this->message->name} <{$this->message->email}>")
            ->line("**Project type:** {$type}")
            ->line('**Budget:** '.($this->message->budgetLabel() ?? 'Not given'))
            ->line('**Timeline:** '.($this->message->timeline?->label() ?? 'Not given'))
            ->line('**Message:**')
            ->line($this->message->message)
            ->action('Open in inbox', route('admin.messages.show', $this->message));
    }
}
