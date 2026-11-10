<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Experience;

use App\Domain\Experience\DataTransferObjects\EducationData;
use App\Domain\Experience\Models\Education;
use Illuminate\Foundation\Http\FormRequest;

class StoreEducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Education::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'institution' => ['required', 'string', 'max:150'],
            'qualification' => ['required', 'string', 'max:150'],
            'field_of_study' => ['nullable', 'string', 'max:150'],
            'started_on' => ['required', 'date_format:Y-m'],
            'ended_on' => ['nullable', 'date_format:Y-m', 'after_or_equal:started_on'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
        ];
    }

    public function toData(): EducationData
    {
        return EducationData::fromValidated($this->validated());
    }
}
