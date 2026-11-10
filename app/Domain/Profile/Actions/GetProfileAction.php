<?php

declare(strict_types=1);

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\DataTransferObjects\SiteProfileData;
use App\Domain\Profile\Models\Profile;
use App\Domain\Profile\Models\SocialLink;
use Illuminate\Support\Facades\Cache;

/**
 * Reads the singleton profile, creating a placeholder on a fresh install.
 */
final class GetProfileAction
{
    public const CACHE_KEY = 'site.profile';

    public function execute(): Profile
    {
        return Profile::query()->firstOrCreate([], [
            'name' => (string) config('app.name'),
            'headline' => 'Software developer',
        ]);
    }

    /**
     * Snapshot for shared Inertia props; invalidated by ProfileChanged.
     */
    public function snapshot(): SiteProfileData
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): SiteProfileData => SiteProfileData::from(
            $this->execute(),
            SocialLink::query()->ordered()->get(),
        ));
    }
}
