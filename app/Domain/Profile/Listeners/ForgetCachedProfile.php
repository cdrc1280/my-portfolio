<?php

declare(strict_types=1);

namespace App\Domain\Profile\Listeners;

use App\Domain\Profile\Actions\GetProfileAction;
use App\Domain\Profile\Events\ProfileChanged;
use Illuminate\Support\Facades\Cache;

final class ForgetCachedProfile
{
    public function handle(ProfileChanged $event): void
    {
        Cache::forget(GetProfileAction::CACHE_KEY);
    }
}
