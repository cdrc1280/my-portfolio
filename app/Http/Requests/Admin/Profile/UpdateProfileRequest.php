<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Profile;

use App\Domain\Profile\DataTransferObjects\ProfileData;
use App\Domain\Profile\Models\Profile;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', Profile::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'headline' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:20000'],
            'location' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'availability' => ['nullable', 'string', 'max:150'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_avatar' => ['boolean'],
            'resume' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'remove_resume' => ['boolean'],
        ];
    }

    public function toData(): ProfileData
    {
        return ProfileData::fromValidated($this->validated());
    }
}
