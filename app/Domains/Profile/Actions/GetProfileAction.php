<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use App\Domains\Profile\Models\Profile;
use App\Domains\Profile\Models\SocialLink;
use App\Domains\Profile\ValueObjects\SiteProfileData;
use Illuminate\Support\Facades\Cache;

/**
 * Reads the singleton profile, creating a placeholder on a fresh install.
 */
final class GetProfileAction
{
    /** Versioned: bump when SiteProfileData's shape changes so stale serialized snapshots are ignored. */
    public const CACHE_KEY = 'site.profile.v4';

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
