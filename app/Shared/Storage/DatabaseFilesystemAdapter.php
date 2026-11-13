<?php

declare(strict_types=1);

namespace App\Shared\Storage;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use stdClass;

/**
 * A Flysystem adapter that keeps files in the `stored_files` table. It backs the `database` disk,
 * used for uploads on hosts without a persistent disk (Vercel). Uploads here are small and rare
 * (a résumé PDF, a few images), which is what a database handles well. Files are served by
 * MediaController at `<url>/<path>`.
 */
final class DatabaseFilesystemAdapter implements FilesystemAdapter
{
    private const TABLE = 'stored_files';

    private readonly FinfoMimeTypeDetector $mimeTypes;

    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly string $baseUrl,
    ) {
        $this->mimeTypes = new FinfoMimeTypeDetector;
    }

    /**
     * Used by Laravel's FilesystemAdapter::url().
     */
    public function getUrl(string $path): string
    {
        return rtrim($this->baseUrl, '/').'/'.ltrim($path, '/');
    }

    public function fileExists(string $path): bool
    {
        return $this->query()->where('path', $this->normalize($path))->exists();
    }

    public function directoryExists(string $path): bool
    {
        return $this->query()->where('path', 'like', $this->directoryPrefix($path).'%')->exists();
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $path = $this->normalize($path);
        $now = Carbon::now();

        $this->query()->upsert([[
            'path' => $path,
            'mime_type' => $config->get('mimetype') ?? $this->mimeTypes->detectMimeType($path, $contents),
            'size' => strlen($contents),
            'contents' => base64_encode($contents),
            'created_at' => $now,
            'updated_at' => $now,
        ]], ['path'], ['mime_type', 'size', 'contents', 'updated_at']);
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, (string) stream_get_contents($contents), $config);
    }

    public function read(string $path): string
    {
        $encoded = $this->query()->where('path', $this->normalize($path))->value('contents');

        if (! is_string($encoded)) {
            throw UnableToReadFile::fromLocation($path, 'File not found.');
        }

        return (string) base64_decode($encoded, true);
    }

    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'w+b');

        if ($stream === false) {
            throw UnableToReadFile::fromLocation($path, 'Could not open a temporary stream.');
        }

        fwrite($stream, $this->read($path));
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $this->query()->where('path', $this->normalize($path))->delete();
    }

    public function deleteDirectory(string $path): void
    {
        $this->query()->where('path', 'like', $this->directoryPrefix($path).'%')->delete();
    }

    public function createDirectory(string $path, Config $config): void
    {
        // Directories are implicit: they exist when a file path starts with them.
    }

    public function setVisibility(string $path, string $visibility): void
    {
        // Every stored file is public (served by MediaController).
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, 'public');
    }

    public function mimeType(string $path): FileAttributes
    {
        $mime = $this->row($path)->mime_type ?? null;

        if (! is_string($mime)) {
            throw UnableToRetrieveMetadata::mimeType($path);
        }

        return new FileAttributes($path, null, null, null, $mime);
    }

    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($path, null, null, Carbon::parse((string) $this->row($path)->updated_at)->getTimestamp());
    }

    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($path, (int) $this->row($path)->size);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $prefix = $this->directoryPrefix($path);
        $directories = [];

        foreach ($this->query()->where('path', 'like', $prefix.'%')->orderBy('path')->get(['path', 'size', 'mime_type', 'updated_at']) as $row) {
            $relative = substr((string) $row->path, strlen($prefix));

            if (! $deep && str_contains($relative, '/')) {
                $directory = $prefix.strtok($relative, '/');

                if (! isset($directories[$directory])) {
                    $directories[$directory] = true;

                    yield new DirectoryAttributes($directory);
                }

                continue;
            }

            yield new FileAttributes((string) $row->path, (int) $row->size, 'public', Carbon::parse((string) $row->updated_at)->getTimestamp(), $row->mime_type);
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->copy($source, $destination, $config);
        $this->delete($source);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        $this->write($destination, $this->read($source), $config);
    }

    private function query(): Builder
    {
        return $this->connection->table(self::TABLE);
    }

    private function row(string $path): stdClass
    {
        $row = $this->query()->where('path', $this->normalize($path))->first(['size', 'mime_type', 'updated_at']);

        if (! $row instanceof stdClass) {
            throw UnableToRetrieveMetadata::create($path, 'metadata', 'File not found.');
        }

        return $row;
    }

    private function normalize(string $path): string
    {
        return ltrim(str_replace('\\', '/', $path), '/');
    }

    private function directoryPrefix(string $path): string
    {
        $path = trim($this->normalize($path), '/');

        return $path === '' ? '' : $path.'/';
    }
}
