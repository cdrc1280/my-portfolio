<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Experience;

use App\Domain\Experience\Models\Education;

class UpdateEducationRequest extends StoreEducationRequest
{
    public function authorize(): bool
    {
        /** @var Education $education */
        $education = $this->route('education');

        return $this->user()?->can('update', $education) ?? false;
    }
}
