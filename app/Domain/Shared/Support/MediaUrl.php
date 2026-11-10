<?php

declare(strict_types=1);

namespace App\Domain\Shared\Support;

use App\Domain\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Turns a stored media path into a public URL. Absolute URLs and
 * root-relative paths (sample content in public/) pass through untouched.
 */
final class MediaUrl
{
    public static function for(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return Storage::disk(StoreUploadedFileAction::disk())->url($path);
    }
}
