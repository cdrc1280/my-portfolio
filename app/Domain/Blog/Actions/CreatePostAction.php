<?php

declare(strict_types=1);

namespace App\Domain\Blog\Actions;

use App\Domain\Blog\DataTransferObjects\PostData;
use App\Domain\Blog\Models\Post;

final class CreatePostAction
{
    public function __construct(
        private readonly SavePostAction $save,
    ) {}

    public function execute(PostData $data): Post
    {
        return $this->save->execute(new Post, $data);
    }
}
