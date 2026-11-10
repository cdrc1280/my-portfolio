<?php

declare(strict_types=1);

namespace App\Domain\Contact\Actions;

use App\Domain\Contact\DataTransferObjects\ContactMessageData;
use App\Domain\Contact\Events\ContactMessageSubmitted;
use App\Domain\Contact\Models\ContactMessage;
use Illuminate\Support\Facades\DB;

/**
 * Stores a visitor's message and triggers the (queued) owner notification.
 */
final class SubmitContactMessageAction
{
    public function execute(ContactMessageData $data): ContactMessage
    {
        return DB::transaction(function () use ($data): ContactMessage {
            $message = ContactMessage::query()->create($data->toArray());

            ContactMessageSubmitted::dispatch($message);

            return $message;
        });
    }
}
