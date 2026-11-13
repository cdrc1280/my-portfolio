<?php

declare(strict_types=1);

namespace App\Domains\Profile\Listeners;

use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Profile\Events\ProfileChanged;
use Illuminate\Support\Facades\Cache;

final class ForgetCachedProfile
{
    public function handle(ProfileChanged $event): void
    {
        Cache::forget(GetProfileAction::CACHE_KEY);
    }
}
