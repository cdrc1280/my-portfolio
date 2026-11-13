<?php

use App\Domains\Projects\Actions\DeleteProjectAction;
use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Models\ProjectImage;
use App\Domains\Tags\Models\Tag;
use Illuminate\Support\Facades\Storage;

test('deletes the project, its images, tag links and files', function () {
    Storage::fake('public');
    Storage::disk('public')->put('projects/cover.jpg', 'x');
    Storage::disk('public')->put('projects/gallery/a.jpg', 'x');

    $project = Project::factory()->create(['cover_image' => 'projects/cover.jpg']);
    $image = ProjectImage::factory()->for($project)->create(['path' => 'projects/gallery/a.jpg']);
    $tag = Tag::factory()->create();
    $project->tags()->attach($tag);

    app(DeleteProjectAction::class)->execute($project);

    $this->assertModelMissing($project);
    $this->assertModelMissing($image);
    $this->assertModelExists($tag);
    $this->assertDatabaseCount('taggables', 0);
    Storage::disk('public')->assertMissing(['projects/cover.jpg', 'projects/gallery/a.jpg']);
});
