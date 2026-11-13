<?php

use App\Domains\Projects\Actions\UpdateProjectAction;
use App\Domains\Projects\Data\ProjectData;
use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Models\ProjectImage;
use App\Domains\Tags\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

function updateData(Project $project, array $overrides = []): ProjectData
{
    return new ProjectData(...array_merge([
        'title' => $project->title,
        'slug' => $project->slug,
        'summary' => $project->summary,
        'status' => $project->status,
    ], $overrides));
}

test('updates attributes and syncs tags', function () {
    $project = Project::factory()->create();
    $project->tags()->attach(Tag::factory()->create());
    $tag = Tag::factory()->create();

    app(UpdateProjectAction::class)->execute($project, updateData($project, [
        'title' => 'Renamed',
        'tagIds' => [$tag->id],
    ]));

    expect($project->fresh())->title->toBe('Renamed')
        ->and($project->tags->modelKeys())->toBe([$tag->id]);
});

test('keeps the original publish date across edits', function () {
    $project = Project::factory()->published()->create(['published_at' => now()->subYear()]);
    $original = $project->published_at->toIso8601String();

    app(UpdateProjectAction::class)->execute($project, updateData($project, ['summary' => 'Edited']));

    expect($project->fresh()->published_at->toIso8601String())->toBe($original);
});

test('clears the publish date when moved back to draft', function () {
    $project = Project::factory()->published()->create();

    app(UpdateProjectAction::class)->execute($project, updateData($project, ['status' => ProjectStatus::Draft]));

    expect($project->fresh()->published_at)->toBeNull();
});

test('replacing the cover deletes the old file', function () {
    Storage::disk('public')->put('projects/old.jpg', 'x');
    $project = Project::factory()->create(['cover_image' => 'projects/old.jpg']);

    app(UpdateProjectAction::class)->execute($project, updateData($project, [
        'cover' => UploadedFile::fake()->image('new.jpg'),
    ]));

    Storage::disk('public')->assertMissing('projects/old.jpg');
    Storage::disk('public')->assertExists($project->fresh()->cover_image);
});

test('removing the cover clears it', function () {
    Storage::disk('public')->put('projects/old.jpg', 'x');
    $project = Project::factory()->create(['cover_image' => 'projects/old.jpg']);

    app(UpdateProjectAction::class)->execute($project, updateData($project, ['removeCover' => true]));

    expect($project->fresh()->cover_image)->toBeNull();
    Storage::disk('public')->assertMissing('projects/old.jpg');
});

test('removes only gallery images belonging to the project', function () {
    $project = Project::factory()->create();
    $own = ProjectImage::factory()->for($project)->create();
    $foreign = ProjectImage::factory()->create();

    app(UpdateProjectAction::class)->execute($project, updateData($project, [
        'removedImageIds' => [$own->id, $foreign->id],
    ]));

    $this->assertModelMissing($own);
    $this->assertModelExists($foreign);
});
