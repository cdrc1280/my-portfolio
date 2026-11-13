<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use App\Domains\Profile\Data\EngagementSettingsData;
use App\Domains\Profile\Events\ProfileChanged;
use App\Domains\Profile\Models\Profile;
use Illuminate\Support\Facades\DB;

final class UpdateEngagementSettingsAction
{
    public function __construct(
        private readonly GetProfileAction $getProfile,
    ) {}

    public function execute(EngagementSettingsData $data): Profile
    {
        $profile = DB::transaction(function () use ($data): Profile {
            $profile = $this->getProfile->execute();
            $profile->update([
                'starting_rate' => $data->startingRate,
                'engagement_types' => $data->engagementTypes,
                'process_steps' => $data->processSteps,
            ]);

            return $profile;
        });

        ProfileChanged::dispatch();

        return $profile;
    }
}
