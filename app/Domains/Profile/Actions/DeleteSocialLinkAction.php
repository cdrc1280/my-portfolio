<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use App\Domains\Profile\Events\ProfileChanged;
use App\Domains\Profile\Models\SocialLink;

final class DeleteSocialLinkAction
{
    public function execute(SocialLink $link): void
    {
        $link->delete();

        ProfileChanged::dispatch();
    }
}
