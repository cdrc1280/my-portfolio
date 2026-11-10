<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Experience;

use App\Domain\Experience\Models\Experience;

class UpdateExperienceRequest extends StoreExperienceRequest
{
    public function authorize(): bool
    {
        /** @var Experience $experience */
        $experience = $this->route('experience');

        return $this->user()?->can('update', $experience) ?? false;
    }
}
