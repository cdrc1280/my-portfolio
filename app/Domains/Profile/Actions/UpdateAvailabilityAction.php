<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Domains\Profile\Events\ProfileChanged;
use App\Domains\Profile\Models\Profile;

/**
 * Quick availability switch from the dashboard (status + optional note).
 */
final class UpdateAvailabilityAction
{
    public function __construct(
        private readonly GetProfileAction $getProfile,
    ) {}

    public function execute(AvailabilityStatus $status, ?string $note): Profile
    {
        $profile = $this->getProfile->execute();
        $profile->update(['availability_status' => $status, 'availability' => $note]);

        ProfileChanged::dispatch();

        return $profile;
    }
}
