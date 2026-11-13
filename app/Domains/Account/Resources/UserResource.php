<?php

declare(strict_types=1);

namespace App\Domains\Account\Resources;

use App\Models\User;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Resource;

/**
 * The signed-in user, shared on every page as `auth.user`.
 */
#[MapName(SnakeCaseMapper::class)]
final class UserResource extends Resource
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public bool $isAdmin,
        public ?string $emailVerifiedAt,
        public bool $twoFactorEnabled,
        public ?string $createdAt,
        public ?string $updatedAt,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            isAdmin: (bool) $user->is_admin,
            emailVerifiedAt: $user->email_verified_at?->toIso8601String(),
            twoFactorEnabled: $user->two_factor_confirmed_at !== null,
            createdAt: $user->created_at?->toIso8601String(),
            updatedAt: $user->updated_at?->toIso8601String(),
        );
    }
}
