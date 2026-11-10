<?php

declare(strict_types=1);

namespace App\Domain\Blog\Actions;

use App\Domain\Blog\Models\Post;
use App\Domain\Shared\Actions\DeleteStoredFileAction;
use Illuminate\Support\Facades\DB;

final class DeletePostAction
{
    public function __construct(
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Post $post): void
    {
        $cover = $post->cover_image;

        DB::transaction(function () use ($post): void {
            $post->tags()->detach();
            $post->delete();
        });

        $this->deleteFile->execute($cover);
    }
}
