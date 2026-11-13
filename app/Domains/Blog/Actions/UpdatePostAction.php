<?php

declare(strict_types=1);

namespace App\Domains\Blog\Actions;

use App\Domains\Blog\Data\PostData;
use App\Domains\Blog\Models\Post;

final class UpdatePostAction
{
    public function __construct(
        private readonly SavePostAction $save,
    ) {}

    public function execute(Post $post, PostData $data): Post
    {
        return $this->save->execute($post, $data);
    }
}
