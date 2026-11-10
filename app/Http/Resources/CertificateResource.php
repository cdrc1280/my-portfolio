<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Experience\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Certificate
 */
class CertificateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'issuer' => $this->issuer,
            'issued_on' => $this->issued_on->format('Y-m'),
            'credential_url' => $this->credential_url,
        ];
    }
}
