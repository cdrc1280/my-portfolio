<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Projects;

use App\Domain\Projects\DataTransferObjects\ProjectData;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public const MAX_IMAGE_KB = 4096;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Project::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug((string) ($this->input('slug') ?: $this->input('title'))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_IMAGE_KB];

        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash', Rule::unique('projects', 'slug')->ignore($this->ignoredId())],
            'summary' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string', 'max:50000'],
            'role' => ['nullable', 'string', 'max:150'],
            'period' => ['nullable', 'string', 'max:50'],
            'highlights' => ['nullable', 'array', 'max:12'],
            'highlights.*' => ['nullable', 'string', 'max:300'],
            'live_url' => ['nullable', 'url:http,https', 'max:255'],
            'repo_url' => ['nullable', 'url:http,https', 'max:255'],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'is_featured' => ['boolean'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'distinct', Rule::exists('tags', 'id')],
            'cover' => ['nullable', ...$image],
            'remove_cover' => ['boolean'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => $image,
            'removed_image_ids' => ['nullable', 'array'],
            'removed_image_ids.*' => ['integer'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'live_url' => 'live URL',
            'repo_url' => 'repository URL',
            'tag_ids' => 'tags',
            'tag_ids.*' => 'tag',
            'images.*' => 'image',
            'highlights.*' => 'highlight',
        ];
    }

    public function toData(): ProjectData
    {
        return ProjectData::fromValidated($this->validated());
    }

    protected function ignoredId(): ?int
    {
        return null;
    }
}
