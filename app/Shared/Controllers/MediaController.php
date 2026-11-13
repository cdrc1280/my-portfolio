<?php

declare(strict_types=1);

namespace App\Shared\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

/**
 * Serves uploads kept on the `database` disk (see DatabaseFilesystemAdapter). Upload paths are
 * unique hashed names, so a file never changes and can be cached for a year.
 */
class MediaController extends Controller
{
    public function __invoke(string $path): Response
    {
        $disk = Storage::disk('database');

        abort_unless($disk->exists($path), 404);

        $contents = (string) $disk->get($path);

        return response($contents, 200, [
            'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
            'Content-Length' => (string) strlen($contents),
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
