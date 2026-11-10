<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Blog;

use App\Domain\Blog\Models\Post;

class UpdatePostRequest extends StorePostRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->editedPost()) ?? false;
    }

    protected function ignoredId(): ?int
    {
        return $this->editedPost()->id;
    }

    private function editedPost(): Post
    {
        /** @var Post */
        return $this->route('post');
    }
}
