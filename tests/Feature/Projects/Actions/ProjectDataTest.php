<?php

use App\Domains\Projects\Actions\CreateProjectAction;
use App\Domains\Projects\Data\ProjectData;
use App\Domains\Projects\Enums\ProjectStatus;

test('builds from input and normalizes values', function () {
    $data = ProjectData::from([
        'title' => ' Site ',
        'slug' => 'site',
        'summary' => 'Summary',
        'problem' => '   ',
        'role' => ' Lead ',
        'architecture_tree' => "app/\n  Domain/  # business logic\ncomposer.json",
        'metrics' => [
            ['label' => ' Load time ', 'value' => ' -60% '],
            ['label' => 'Missing value', 'value' => ''],
            ['label' => '', 'value' => '12'],
        ],
        'is_confidential' => '1',
        'started_on' => '2024-03',
        'ended_on' => '',
        'status' => 'published',
        'is_featured' => '1',
        'tag_ids' => ['3', '5'],
        'removed_image_ids' => ['9'],
    ]);

    expect($data)
        ->title->toBe('Site')
        ->problem->toBeNull()
        ->role->toBe('Lead')
        ->isConfidential->toBeTrue()
        ->status->toBe(ProjectStatus::Published)
        ->isFeatured->toBeTrue()
        ->tagIds->toBe([3, 5])
        ->removedImageIds->toBe([9])
        ->cover->toBeNull()
        ->diagram->toBeNull()
        ->endedOn->toBeNull();

    expect($data->startedOn?->format('Y-m-d'))->toBe('2024-03-01');
    expect($data->completeMetrics())->toBe([['label' => 'Load time', 'value' => '-60%']]);
    expect($data->folderTree())->toBe([
        ['name' => 'app', 'children' => [['name' => 'Domain', 'note' => 'business logic', 'children' => []]]],
        ['name' => 'composer.json'],
    ]);
});

test('columns exclude files and relations and store empty json as null', function () {
    $attributes = CreateProjectAction::columns(new ProjectData(title: 'A', slug: 'a', summary: 'S'));

    expect($attributes)->not->toHaveKeys(['cover', 'diagram', 'tag_ids', 'images'])
        ->and($attributes['metrics'])->toBeNull()
        ->and($attributes['architecture_tree'])->toBeNull();
});
