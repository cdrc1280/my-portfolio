<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Profile;

use App\Domain\Profile\Models\SocialLink;

class UpdateSocialLinkRequest extends StoreSocialLinkRequest
{
    public function authorize(): bool
    {
        /** @var SocialLink $link */
        $link = $this->route('social_link');

        return $this->user()?->can('update', $link) ?? false;
    }
}
