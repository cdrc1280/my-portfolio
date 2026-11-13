<?php

declare(strict_types=1);

namespace App\Domains\Projects\Actions;

use App\Domains\Projects\Models\Project;
use App\Shared\Actions\DeleteStoredFileAction;
use Illuminate\Support\Facades\DB;

final class DeleteProjectAction
{
    public function __construct(
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Project $project): void
    {
        $paths = [$project->cover_image, $project->diagram_path, ...$project->images()->pluck('path')->all()];

        DB::transaction(function () use ($project): void {
            $project->tags()->detach();
            $project->delete();
        });

        foreach ($paths as $path) {
            $this->deleteFile->execute($path);
        }
    }
}
