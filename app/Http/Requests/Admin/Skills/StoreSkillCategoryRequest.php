<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Skills;

use App\Domain\Skills\DataTransferObjects\SkillCategoryData;
use App\Domain\Skills\Models\SkillCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSkillCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', SkillCategory::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('skill_categories', 'name')->ignore($this->ignoredId())],
        ];
    }

    public function toData(): SkillCategoryData
    {
        /** @var array{name: string} $validated */
        $validated = $this->validated();

        return SkillCategoryData::fromArray($validated);
    }

    protected function ignoredId(): ?int
    {
        return null;
    }
}
