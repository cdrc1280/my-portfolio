<?php

declare(strict_types=1);

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\DataTransferObjects\ProjectData;
use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Actions\DeleteStoredFileAction;
use App\Domain\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Support\Facades\DB;

/**
 * Applies cover and gallery changes from ProjectData. Called inside the
 * create/update transactions; file deletions are deferred until commit.
 */
final class SyncProjectMediaAction
{
    private const DIRECTORY = 'projects';

    public function __construct(
        private readonly StoreUploadedFileAction $storeFile,
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Project $project, ProjectData $data): void
    {
        $this->syncCover($project, $data);
        $this->removeImages($project, $data->removedImageIds);
        $this->addImages($project, $data);
    }

    private function syncCover(Project $project, ProjectData $data): void
    {
        $previous = $project->cover_image;

        if ($data->cover !== null) {
            $project->update(['cover_image' => $this->storeFile->execute($data->cover, self::DIRECTORY)]);
        } elseif ($data->removeCover) {
            $project->update(['cover_image' => null]);
        } else {
            return;
        }

        $this->deleteAfterCommit($previous);
    }

    /**
     * @param  list<int>  $ids
     */
    private function removeImages(Project $project, array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $images = $project->images()->whereKey($ids)->get();

        foreach ($images as $image) {
            $image->delete();
            $this->deleteAfterCommit($image->path);
        }
    }

    private function addImages(Project $project, ProjectData $data): void
    {
        $position = (int) $project->images()->max('position');

        foreach ($data->newImages as $file) {
            $project->images()->create([
                'path' => $this->storeFile->execute($file, self::DIRECTORY.'/gallery'),
                'alt' => "{$data->title} screenshot",
                'position' => ++$position,
            ]);
        }
    }

    private function deleteAfterCommit(?string $path): void
    {
        if ($path !== null) {
            DB::afterCommit(fn () => $this->deleteFile->execute($path));
        }
    }
}
