<?php

declare(strict_types=1);

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\DataTransferObjects\ProfileData;
use App\Domain\Profile\Events\ProfileChanged;
use App\Domain\Profile\Models\Profile;
use App\Domain\Shared\Actions\DeleteStoredFileAction;
use App\Domain\Shared\Actions\StoreUploadedFileAction;
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

        $attributes = $data->attributes();

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

        DB::transaction(fn () => $profile->update($attributes));

        foreach ($obsolete as $path) {
            $this->deleteFile->execute($path);
        }

        ProfileChanged::dispatch();

        return $profile;
    }
}
