<?php

declare(strict_types=1);

namespace App\Domains\Blog\Actions;

use App\Domains\Blog\Data\PostData;
use App\Domains\Blog\Enums\PostStatus;
use App\Domains\Blog\Models\Post;
use App\Shared\Actions\DeleteStoredFileAction;
use App\Shared\Actions\StoreUploadedFileAction;
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
                'title' => $data->title,
                'slug' => $data->slug,
                'excerpt' => $data->excerpt,
                'body' => $data->body,
                'status' => $data->status,
                'seo_title' => $data->seoTitle,
                'seo_description' => $data->seoDescription,
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
