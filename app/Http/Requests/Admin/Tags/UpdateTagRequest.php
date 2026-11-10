<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tags;

use App\Domain\Tags\Models\Tag;

class UpdateTagRequest extends StoreTagRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->tag()) ?? false;
    }

    protected function ignoredId(): ?int
    {
        return $this->tag()->id;
    }

    private function tag(): Tag
    {
        /** @var Tag */
        return $this->route('tag');
    }
}
