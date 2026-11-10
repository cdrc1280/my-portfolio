<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Experience;

use App\Domain\Experience\DataTransferObjects\CertificateData;
use App\Domain\Experience\Models\Certificate;
use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Certificate::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'issuer' => ['required', 'string', 'max:150'],
            'issued_on' => ['required', 'date_format:Y-m'],
            'credential_url' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'issued_on' => 'issue date',
            'credential_url' => 'credential URL',
        ];
    }

    public function toData(): CertificateData
    {
        return CertificateData::fromValidated($this->validated());
    }
}
