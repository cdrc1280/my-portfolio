<?php

declare(strict_types=1);

namespace App\Domain\Projects\Actions;

use App\Domain\Projects\Models\Project;
use App\Domain\Shared\Actions\DeleteStoredFileAction;
use Illuminate\Support\Facades\DB;

final class DeleteProjectAction
{
    public function __construct(
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Project $project): void
    {
        $paths = [$project->cover_image, ...$project->images()->pluck('path')->all()];

        DB::transaction(function () use ($project): void {
            $project->tags()->detach();
            $project->delete();
        });

        foreach ($paths as $path) {
            $this->deleteFile->execute($path);
        }
    }
}
