<?php

declare(strict_types=1);

namespace App\Domain\Shared\Policies;

use App\Models\User;

/**
 * Base policy for CMS-managed content: only administrators may manage it.
 * Domain policies extend this and override abilities that differ.
 */
abstract class AdminPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    public function view(User $user): bool
    {
        return $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    public function update(User $user): bool
    {
        return $user->is_admin;
    }

    public function delete(User $user): bool
    {
        return $user->is_admin;
    }

    public function reorder(User $user): bool
    {
        return $user->is_admin;
    }
}
