<?php

declare(strict_types=1);

namespace App\Shared\Actions;

use Illuminate\Http\UploadedFile;
use RuntimeException;

/**
 * Single entry point for persisting uploads (images, résumé PDF) so the
 * storage disk and naming strategy live in one place.
 */
final class StoreUploadedFileAction
{
    public function execute(UploadedFile $file, string $directory, ?string $replacing = null): string
    {
        $path = $file->store($directory, ['disk' => self::disk()]);

        if ($path === false) {
            throw new RuntimeException("Failed to store upload in [{$directory}].");
        }

        if ($replacing !== null) {
            app(DeleteStoredFileAction::class)->execute($replacing);
        }

        return $path;
    }

    public static function disk(): string
    {
        return (string) config('filesystems.media_disk', 'public');
    }
}
