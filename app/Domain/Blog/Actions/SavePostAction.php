<?php

declare(strict_types=1);

namespace App\Domain\Blog\Actions;

use App\Domain\Blog\DataTransferObjects\PostData;
use App\Domain\Blog\Enums\PostStatus;
use App\Domain\Blog\Models\Post;
use App\Domain\Shared\Actions\DeleteStoredFileAction;
use App\Domain\Shared\Actions\StoreUploadedFileAction;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

/**
 * Shared persistence for create/update: attributes, publish date, tags and
 * cover image, in one transaction. Use CreatePostAction / UpdatePostAction.
 *
 * @internal
 */
final class SavePostAction
{
    public function __construct(
        private readonly StoreUploadedFileAction $storeFile,
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Post $post, PostData $data): Post
    {
        $previousCover = $post->cover_image;
        $replaceCover = $data->cover !== null || $data->removeCover;

        DB::transaction(function () use ($post, $data): void {
            $post->fill([
                ...$data->attributes(),
                'published_at' => $this->publishedAt($post, $data),
            ]);

            if ($data->cover !== null) {
                $post->cover_image = $this->storeFile->execute($data->cover, 'posts');
            } elseif ($data->removeCover) {
                $post->cover_image = null;
            }

            $post->save();
            $post->tags()->sync($data->tagIds);
        });

        if ($replaceCover) {
            $this->deleteFile->execute($previousCover);
        }

        return $post->load('tags');
    }

    /**
     * An explicit date wins (allows back-dating or scheduling); otherwise
     * keep the original date, or stamp "now" on first publish.
     */
    private function publishedAt(Post $post, PostData $data): ?CarbonImmutable
    {
        if ($data->status === PostStatus::Draft) {
            return null;
        }

        return $data->publishedAt ?? $post->published_at ?? CarbonImmutable::now();
    }
}
