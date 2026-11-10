<?php

declare(strict_types=1);

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\Events\ProfileChanged;
use App\Domain\Profile\Models\SocialLink;

final class DeleteSocialLinkAction
{
    public function execute(SocialLink $link): void
    {
        $link->delete();

        ProfileChanged::dispatch();
    }
}
