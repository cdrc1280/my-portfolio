<?php

declare(strict_types=1);

namespace App\Domains\Account\Data;

use App\Models\User;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

/**
 * Account settings: the signed-in user's name and login email.
 */
final class AccountProfileData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore(request()->user()?->id)],
        ];
    }
}
