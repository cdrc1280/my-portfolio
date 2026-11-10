<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Profile;

use App\Domain\Profile\DataTransferObjects\SocialLinkData;
use App\Domain\Profile\Enums\SocialPlatform;
use App\Domain\Profile\Models\SocialLink;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSocialLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', SocialLink::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isEmail = $this->input('platform') === SocialPlatform::Email->value;

        return [
            'platform' => ['required', Rule::enum(SocialPlatform::class)],
            'url' => $isEmail
                ? ['required', 'string', 'max:255', 'regex:/^(mailto:)?[^@\s]+@[^@\s]+\.[^@\s]+$/']
                : ['required', 'url:http,https', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['url.regex' => 'Enter a valid email address.'];
    }

    public function toData(): SocialLinkData
    {
        /** @var array{platform: string, url: string} $validated */
        $validated = $this->validated();

        return SocialLinkData::fromArray($validated);
    }
}
