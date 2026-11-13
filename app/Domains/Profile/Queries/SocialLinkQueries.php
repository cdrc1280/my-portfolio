<?php

declare(strict_types=1);

namespace App\Domains\Profile\Queries;

use App\Domains\Profile\Models\SocialLink;
use App\Domains\Profile\Resources\SocialLinkResource;

/**
 * Read side of social links.
 */
final class SocialLinkQueries
{
    /**
     * @return list<SocialLinkResource>
     */
    public function ordered(): array
    {
        return array_values(SocialLink::query()->ordered()->get()
            ->map(fn (SocialLink $link): SocialLinkResource => SocialLinkResource::fromModel($link))
            ->all());
    }
}
