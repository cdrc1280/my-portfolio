<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Blog;

use App\Domain\Blog\DataTransferObjects\PostData;
use App\Domain\Blog\Enums\PostStatus;
use App\Domain\Blog\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Post::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => Str::slug((string) ($this->input('slug') ?: $this->input('title')))]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:160', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($this->ignoredId())],
            'excerpt' => ['required', 'string', 'max:500'],
            'body' => ['required', 'string', 'max:100000'],
            'status' => ['required', Rule::enum(PostStatus::class)],
            'published_at' => ['nullable', 'date'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['integer', 'distinct', Rule::exists('tags', 'id')],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_cover' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['published_at' => 'publish date', 'tag_ids.*' => 'tag'];
    }

    public function toData(): PostData
    {
        return PostData::fromValidated($this->validated());
    }

    protected function ignoredId(): ?int
    {
        return null;
    }
}
