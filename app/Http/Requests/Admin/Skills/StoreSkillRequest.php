<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Skills;

use App\Domain\Skills\DataTransferObjects\SkillData;
use App\Domain\Skills\Models\Skill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Skill::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'skill_category_id' => ['required', 'integer', Rule::exists('skill_categories', 'id')],
            'name' => [
                'required', 'string', 'max:60',
                Rule::unique('skills', 'name')
                    ->where('skill_category_id', $this->integer('skill_category_id'))
                    ->ignore($this->ignoredId()),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['skill_category_id' => 'category'];
    }

    public function toData(): SkillData
    {
        /** @var array{name: string, skill_category_id: int} $validated */
        $validated = $this->validated();

        return SkillData::fromArray($validated);
    }

    protected function ignoredId(): ?int
    {
        return null;
    }
}
