<?php

declare(strict_types=1);

namespace App\Domains\Contact\Actions;

use App\Domains\Contact\Data\ContactMessageData;
use App\Domains\Contact\Events\ContactMessageSubmitted;
use App\Domains\Contact\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

/**
 * Stores a brief and fires ContactMessageSubmitted (owner email + auto-reply, both queued).
 */
final class SubmitContactMessageAction
{
    public function execute(ContactMessageData $data, ?string $ipAddress = null): ContactMessage
    {
        return DB::transaction(function () use ($data, $ipAddress): ContactMessage {
            $message = ContactMessage::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'project_type' => $data->projectType,
                'project_type_other' => $data->projectTypeOther,
                'budget' => $data->budget,
                'budget_currency' => $data->budgetCurrency,
                'budget_other' => $data->budgetOther,
                'timeline' => $data->timeline,
                'timeline_other' => $data->timelineOther,
                'message' => $data->message,
                'ip_hash' => self::hashIp($ipAddress),
            ]);

            ContactMessageSubmitted::dispatch($message);

            return $message;
        });
    }

    /**
     * A keyed hash so repeat senders can be spotted without storing the raw IP.
     */
    public static function hashIp(?string $ipAddress): ?string
    {
        if ($ipAddress === null || $ipAddress === '') {
            return null;
        }

        return hash_hmac('sha256', $ipAddress, (string) config('app.key'));
    }
}
