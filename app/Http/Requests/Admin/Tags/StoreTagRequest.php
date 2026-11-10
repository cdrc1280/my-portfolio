<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tags;

use App\Domain\Tags\DataTransferObjects\TagData;
use App\Domain\Tags\Models\Tag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class StoreTagRequest extends FormRequest
{
    /**
     * True when no slug was submitted and it was derived from the name.
     */
    private bool $slugDerived = false;

    public function authorize(): bool
    {
        return $this->user()?->can('create', Tag::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->slugDerived = blank($this->input('slug'));

        $this->merge(['slug' => Str::slug((string) ($this->input('slug') ?: $this->input('name')))]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['required', 'string', 'max:60', 'alpha_dash'],
        ];
    }

    /**
     * Slug uniqueness is reported on `name` when the slug was derived from it,
     * so a create form that only has a name field can show the error.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $taken = Tag::query()
                    ->where('slug', $this->input('slug'))
                    ->when($this->ignoredId(), fn ($query, int $id) => $query->whereKeyNot($id))
                    ->exists();

                if ($taken) {
                    $validator->errors()->add($this->slugDerived ? 'name' : 'slug', 'A tag with this name already exists.');
                }
            },
        ];
    }

    public function toData(): TagData
    {
        /** @var array{name: string, slug: string} $validated */
        $validated = $this->validated();

        return TagData::fromArray($validated);
    }

    protected function ignoredId(): ?int
    {
        return null;
    }
}
