<?php

declare(strict_types=1);

namespace App\Domain\Contact\DataTransferObjects;

use App\Domain\Shared\Support\Input;
use Illuminate\Support\Str;

final readonly class ContactMessageData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $message,
        public ?string $subject = null,
        public ?string $ipAddress = null,
        public ?string $userAgent = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated, ?string $ipAddress = null, ?string $userAgent = null): self
    {
        return new self(
            name: trim((string) $validated['name']),
            email: Str::lower(trim((string) $validated['email'])),
            message: trim((string) $validated['message']),
            subject: Input::nullableString($validated['subject'] ?? null),
            ipAddress: $ipAddress,
            userAgent: $userAgent !== null ? Str::limit($userAgent, 490, '') : null,
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
        ];
    }
}
