<?php

use App\Domain\Contact\Actions\SubmitContactMessageAction;
use App\Domain\Contact\DataTransferObjects\ContactMessageData;
use App\Domain\Contact\Events\ContactMessageSubmitted;
use App\Domain\Contact\Listeners\SendContactMessageNotification;
use App\Domain\Contact\Notifications\NewContactMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

test('stores the message and dispatches the event', function () {
    Event::fake([ContactMessageSubmitted::class]);

    $message = app(SubmitContactMessageAction::class)->execute(ContactMessageData::fromValidated([
        'name' => ' Ada ',
        'email' => 'ADA@Example.com',
        'subject' => '',
        'message' => 'Hello there, nice portfolio!',
    ], '203.0.113.5', 'Firefox'));

    expect($message)
        ->name->toBe('Ada')
        ->email->toBe('ada@example.com')
        ->subject->toBeNull()
        ->ip_address->toBe('203.0.113.5')
        ->isRead()->toBeFalse();
    Event::assertDispatched(ContactMessageSubmitted::class, fn ($event) => $event->message->is($message));
});

test('the queued listener emails the owner with reply-to set', function () {
    Notification::fake();
    config(['portfolio.contact.notify_email' => 'owner@example.com']);

    $message = app(SubmitContactMessageAction::class)->execute(
        new ContactMessageData(name: 'Ada', email: 'ada@example.com', message: 'Hello there!'),
    );

    Notification::assertSentOnDemand(NewContactMessage::class, function (NewContactMessage $notification, array $channels, AnonymousNotifiable $notifiable) use ($message) {
        $mail = $notification->toMail($notifiable);

        return $notifiable->routes['mail'] === 'owner@example.com'
            && $notification->message->is($message)
            && $mail->replyTo === [['ada@example.com', 'Ada']];
    });
});

test('the listener is queued', function () {
    expect(new SendContactMessageNotification)->toBeInstanceOf(ShouldQueue::class);
});

test('nothing is sent when no recipient is configured', function () {
    Notification::fake();
    config(['portfolio.contact.notify_email' => null]);

    app(SubmitContactMessageAction::class)->execute(new ContactMessageData('Ada', 'ada@example.com', 'Hello there!'));

    Notification::assertNothingSent();
});
