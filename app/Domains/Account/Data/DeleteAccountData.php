<?php

declare(strict_types=1);

namespace App\Domains\Account\Data;

use Spatie\LaravelData\Data;

/**
 * Account settings: delete the account, confirmed with the current password.
 */
final class DeleteAccountData extends Data
{
    public function __construct(
        public string $password,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return ['password' => ['required', 'string', 'current_password']];
    }
}
