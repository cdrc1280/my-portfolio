<?php

use App\Domains\Projects\Actions\CreateProjectAction;
use App\Domains\Projects\Data\ProjectData;
use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Models\Tag;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

function projectData(array $overrides = []): ProjectData
{
    return new ProjectData(...array_merge([
        'title' => 'Profiling MIS',
        'slug' => 'profiling-mis',
        'summary' => 'National profiling system.',
    ], $overrides));
}

test('creates a draft project with tags', function () {
    $tags = Tag::factory()->count(2)->create();

    $project = app(CreateProjectAction::class)->execute(projectData(['tagIds' => $tags->modelKeys()]));

    expect($project)
        ->status->toBe(ProjectStatus::Draft)
        ->published_at->toBeNull()
        ->and($project->tags)->toHaveCount(2);
});

test('stamps published_at when created as published', function () {
    $project = app(CreateProjectAction::class)->execute(projectData(['status' => ProjectStatus::Published]));

    expect($project->published_at)->not->toBeNull();
});

test('appends new projects to the end of the order', function () {
    Project::factory()->create(['position' => 7]);

    $project = app(CreateProjectAction::class)->execute(projectData());

    expect($project->position)->toBe(8);
});

test('stores the cover and gallery images', function () {
    $project = app(CreateProjectAction::class)->execute(projectData([
        'cover' => UploadedFile::fake()->image('cover.jpg'),
        'newImages' => [UploadedFile::fake()->image('a.jpg'), UploadedFile::fake()->image('b.jpg')],
    ]));

    Storage::disk('public')->assertExists($project->cover_image);
    expect($project->images)->toHaveCount(2)
        ->and($project->images->pluck('position')->all())->toBe([1, 2]);
    Storage::disk('public')->assertExists($project->images->first()->path);
});

test('rolls back when a tag does not exist', function () {
    expect(fn () => app(CreateProjectAction::class)->execute(projectData(['tagIds' => [999]])))
        ->toThrow(QueryException::class);

    $this->assertDatabaseCount('projects', 0);
});
