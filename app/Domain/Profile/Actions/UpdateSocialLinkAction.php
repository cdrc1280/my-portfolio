<?php

declare(strict_types=1);

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\DataTransferObjects\SocialLinkData;
use App\Domain\Profile\Events\ProfileChanged;
use App\Domain\Profile\Models\SocialLink;

final class UpdateSocialLinkAction
{
    public function execute(SocialLink $link, SocialLinkData $data): SocialLink
    {
        $link->update($data->toArray());

        ProfileChanged::dispatch();

        return $link;
    }
}
