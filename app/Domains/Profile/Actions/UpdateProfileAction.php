<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use App\Domains\Profile\Data\ProfileData;
use App\Domains\Profile\Events\ProfileChanged;
use App\Domains\Profile\Models\Profile;
use App\Shared\Actions\DeleteStoredFileAction;
use App\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Support\Facades\DB;

final class UpdateProfileAction
{
    public function __construct(
        private readonly GetProfileAction $getProfile,
        private readonly StoreUploadedFileAction $storeFile,
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(ProfileData $data): Profile
    {
        $profile = $this->getProfile->execute();
        $obsolete = [];

        $attributes = [
            'name' => $data->name,
            'headline' => $data->headline,
            'tagline' => $data->tagline,
            'short_bio' => $data->shortBio,
            'bio' => $data->bio,
            'location' => $data->location,
            'timezone' => $data->timezone,
            'email' => $data->email,
            'phone' => $data->phone,
            'availability_status' => $data->availabilityStatus,
            'availability' => $data->availability,
            'working_hours' => $data->workingHours,
            'response_time' => $data->responseTime,
            'booking_url' => $data->bookingUrl,
            'currently_learning' => $data->currentlyLearning,
            'blog_enabled' => $data->blogEnabled,
            'assistant_enabled' => $data->assistantEnabled,
            'seo_title' => $data->seoTitle,
            'seo_description' => $data->seoDescription,
        ];

        if ($data->avatar !== null) {
            $attributes['avatar_path'] = $this->storeFile->execute($data->avatar, 'profile');
            $obsolete[] = $profile->avatar_path;
        } elseif ($data->removeAvatar) {
            $attributes['avatar_path'] = null;
            $obsolete[] = $profile->avatar_path;
        }

        if ($data->resume !== null) {
            $attributes['resume_path'] = $this->storeFile->execute($data->resume, 'resume');
            $attributes['resume_updated_at'] = now();
            $obsolete[] = $profile->resume_path;
        } elseif ($data->removeResume) {
            $attributes['resume_path'] = null;
            $attributes['resume_updated_at'] = null;
            $obsolete[] = $profile->resume_path;
        }

        if ($data->ogImage !== null) {
            $attributes['og_image_path'] = $this->storeFile->execute($data->ogImage, 'profile');
            $obsolete[] = $profile->og_image_path;
        } elseif ($data->removeOgImage) {
            $attributes['og_image_path'] = null;
            $obsolete[] = $profile->og_image_path;
        }

        DB::transaction(fn () => $profile->update($attributes));

        foreach ($obsolete as $path) {
            $this->deleteFile->execute($path);
        }

        ProfileChanged::dispatch();

        return $profile;
    }
}
