<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Experience;

use App\Domain\Experience\DataTransferObjects\ExperienceData;
use App\Domain\Experience\Enums\EmploymentType;
use App\Domain\Experience\Models\Experience;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Experience::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company' => ['required', 'string', 'max:150'],
            'company_url' => ['nullable', 'url:http,https', 'max:255'],
            'role' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'employment_type' => ['required', Rule::enum(EmploymentType::class)],
            'started_on' => ['required', 'date_format:Y-m'],
            'ended_on' => ['nullable', 'date_format:Y-m', 'after_or_equal:started_on'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'highlights' => ['nullable', 'array', 'max:12'],
            'highlights.*' => ['nullable', 'string', 'max:300'],
            'technologies' => ['nullable', 'array', 'max:20'],
            'technologies.*' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'started_on' => 'start date',
            'ended_on' => 'end date',
            'company_url' => 'company website',
            'highlights.*' => 'highlight',
            'technologies.*' => 'technology',
        ];
    }

    public function toData(): ExperienceData
    {
        return ExperienceData::fromValidated($this->validated());
    }
}
