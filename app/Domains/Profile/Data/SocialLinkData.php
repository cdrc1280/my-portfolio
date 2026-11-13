<?php

declare(strict_types=1);

namespace App\Domains\Profile\Data;

use App\Domains\Profile\Enums\SocialPlatform;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * Create/update input for a social link. The Email platform takes a bare address, stored as mailto:.
 */
final class SocialLinkData extends Data
{
    public function __construct(
        public SocialPlatform $platform,
        public string $url,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public static function prepareForPipeline(array $properties): array
    {
        $url = trim((string) ($properties['url'] ?? ''));

        if (($properties['platform'] ?? null) === SocialPlatform::Email->value && $url !== '' && ! str_starts_with($url, 'mailto:')) {
            $url = "mailto:{$url}";
        }

        return [...$properties, 'url' => $url];
    }

    /**
     * @return array<string, mixed>
     */
    public static function rules(?ValidationContext $context = null): array
    {
        $isEmail = ($context?->payload['platform'] ?? null) === SocialPlatform::Email->value;

        return [
            'platform' => ['required', Rule::enum(SocialPlatform::class)],
            'url' => $isEmail
                ? ['required', 'string', 'max:255', 'regex:/^mailto:[^@\s]+@[^@\s]+\.[^@\s]+$/']
                : ['required', 'url:http,https', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function messages(): array
    {
        return ['url.regex' => 'Enter a valid email address.'];
    }
}
