<?php

declare(strict_types=1);

namespace App\Domain\Shared\Actions;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class DeleteStoredFileAction
{
    public function execute(?string $path): void
    {
        // Seeded/sample content may reference absolute URLs; those aren't ours to delete.
        if ($path === null || $path === '' || Str::startsWith($path, ['http://', 'https://', '/'])) {
            return;
        }

        Storage::disk(StoreUploadedFileAction::disk())->delete($path);
    }
}
