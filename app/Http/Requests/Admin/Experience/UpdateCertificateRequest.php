<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Experience;

use App\Domain\Experience\Models\Certificate;

class UpdateCertificateRequest extends StoreCertificateRequest
{
    public function authorize(): bool
    {
        /** @var Certificate $certificate */
        $certificate = $this->route('certificate');

        return $this->user()?->can('update', $certificate) ?? false;
    }
}
