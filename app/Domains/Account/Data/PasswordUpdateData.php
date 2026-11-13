<?php

declare(strict_types=1);

namespace App\Domains\Account\Data;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Data;

/**
 * Account settings: change password (current password required, new one confirmed).
 */
final class PasswordUpdateData extends Data
{
    public function __construct(
        public string $password,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
        ];
    }
}
