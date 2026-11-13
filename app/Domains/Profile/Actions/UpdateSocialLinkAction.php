<?php

declare(strict_types=1);

namespace App\Domains\Profile\Actions;

use App\Domains\Profile\Data\SocialLinkData;
use App\Domains\Profile\Events\ProfileChanged;
use App\Domains\Profile\Models\SocialLink;

final class UpdateSocialLinkAction
{
    public function execute(SocialLink $link, SocialLinkData $data): SocialLink
    {
        $link->update(['platform' => $data->platform, 'url' => $data->url]);

        ProfileChanged::dispatch();

        return $link;
    }
}
