<?php

declare(strict_types=1);

namespace App\Domain\Profile\Actions;

use App\Domain\Profile\DataTransferObjects\SocialLinkData;
use App\Domain\Profile\Events\ProfileChanged;
use App\Domain\Profile\Models\SocialLink;

final class CreateSocialLinkAction
{
    public function execute(SocialLinkData $data): SocialLink
    {
        $link = SocialLink::query()->create($data->toArray());

        ProfileChanged::dispatch();

        return $link;
    }
}
