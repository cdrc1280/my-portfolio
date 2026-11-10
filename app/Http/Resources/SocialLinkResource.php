<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Profile\Models\SocialLink;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SocialLink
 */
class SocialLinkResource extends JsonResource
{
    /**
     * @return array{id: int, platform: string, label: string, url: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform' => $this->platform->value,
            'label' => $this->platform->label(),
            'url' => $this->url,
        ];
    }
}
