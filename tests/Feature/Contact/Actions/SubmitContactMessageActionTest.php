<?php

use App\Domains\Contact\Actions\SubmitContactMessageAction;
use App\Domains\Contact\Data\ContactMessageData;
use App\Domains\Contact\Enums\BudgetRange;
use App\Domains\Contact\Enums\ProjectTimeline;
use App\Domains\Contact\Enums\ProjectType;
use App\Domains\Contact\Events\ContactMessageSubmitted;
use App\Domains\Contact\Listeners\SendContactAutoReply;
use App\Domains\Contact\Listeners\SendContactMessageNotification;
use App\Domains\Contact\Notifications\ContactMessageReceived;
use App\Domains\Contact\Notifications\NewContactMessage;
use App\Domains\Profile\Models\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

test('stores the brief with a hashed ip and dispatches the event', function () {
    Event::fake([ContactMessageSubmitted::class]);

    $message = app(SubmitContactMessageAction::class)->execute(ContactMessageData::from([
        'name' => ' Ada ',
        'email' => 'ADA@Example.com',
        'project_type' => 'new_build',
        'budget' => '3k_8k',
        'budget_currency' => 'PHP',
        'timeline' => 'within_month',
        'message' => 'Hello there, nice portfolio!',
    ]), '203.0.113.5');

    expect($message)
        ->name->toBe('Ada')
        ->email->toBe('ada@example.com')
        ->project_type->toBe(ProjectType::NewBuild)
        ->budget->toBe(BudgetRange::From3kTo8k)
        ->budgetLabel()->toBe('₱150,000 – ₱450,000')
        ->timeline->toBe(ProjectTimeline::WithinMonth)
        ->ip_hash->toBe(SubmitContactMessageAction::hashIp('203.0.113.5'))
        ->isRead()->toBeFalse();
    expect($message->ip_hash)->not->toContain('203.0.113.5')->toHaveLength(64);
    Event::assertDispatched(ContactMessageSubmitted::class, fn ($event) => $event->message->is($message));
});

test('the owner gets an email with the brief and reply-to set', function () {
    Notification::fake();
    config(['portfolio.contact.notify_email' => 'owner@example.com']);

    $message = app(SubmitContactMessageAction::class)->execute(new ContactMessageData(
        name: 'Ada', email: 'ada@example.com', message: 'Hello there!', projectType: ProjectType::Maintenance,
    ));

    Notification::assertSentOnDemand(NewContactMessage::class, function (NewContactMessage $notification, array $channels, AnonymousNotifiable $notifiable) use ($message) {
        $mail = $notification->toMail($notifiable);

        return $notifiable->routes['mail'] === 'owner@example.com'
            && $notification->message->is($message)
            && $mail->replyTo === [['ada@example.com', 'Ada']]
            && str_contains((string) $mail->subject, 'Maintenance & support');
    });
});

test('the sender gets an auto-reply with the response time', function () {
    Notification::fake();
    Profile::factory()->create(['name' => 'Cedric', 'response_time' => 'within two working days']);

    app(SubmitContactMessageAction::class)->execute(new ContactMessageData('Ada', 'ada@example.com', 'Hello there!'));

    Notification::assertSentOnDemand(ContactMessageReceived::class, function (ContactMessageReceived $notification, array $channels, AnonymousNotifiable $notifiable) {
        $mail = $notification->toMail($notifiable);

        return $notifiable->routes['mail'] === ['ada@example.com' => 'Ada']
            && in_array('You can expect a reply within two working days.', $mail->introLines, true)
            && str_contains((string) $mail->salutation, 'Cedric');
    });
});

test('both listeners are queued', function () {
    expect(new SendContactMessageNotification)->toBeInstanceOf(ShouldQueue::class)
        ->and(app(SendContactAutoReply::class))->toBeInstanceOf(ShouldQueue::class);
});

test('no owner email is sent when no recipient is configured', function () {
    Notification::fake();
    config(['portfolio.contact.notify_email' => null]);

    app(SubmitContactMessageAction::class)->execute(new ContactMessageData('Ada', 'ada@example.com', 'Hello there!'));

    Notification::assertNotSentTo(new AnonymousNotifiable, NewContactMessage::class);
    Notification::assertSentOnDemandTimes(NewContactMessage::class, 0);
});

test('a missing ip is stored as null', function () {
    expect(SubmitContactMessageAction::hashIp(null))->toBeNull()
        ->and(SubmitContactMessageAction::hashIp(''))->toBeNull();
});
