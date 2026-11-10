<?php

use App\Domain\Projects\DataTransferObjects\ProjectData;
use App\Domain\Projects\Enums\ProjectStatus;

test('builds from validated input and normalizes values', function () {
    $data = ProjectData::fromValidated([
        'title' => 'Site',
        'slug' => 'site',
        'summary' => 'Summary',
        'body' => '   ',
        'role' => ' Lead ',
        'highlights' => ['  One ', '', null, 'Two'],
        'status' => 'published',
        'is_featured' => '1',
        'tag_ids' => ['3', '5'],
        'removed_image_ids' => ['9'],
    ]);

    expect($data)
        ->body->toBeNull()
        ->role->toBe('Lead')
        ->highlights->toBe(['One', 'Two'])
        ->status->toBe(ProjectStatus::Published)
        ->isFeatured->toBeTrue()
        ->tagIds->toBe([3, 5])
        ->removedImageIds->toBe([9])
        ->cover->toBeNull();
});

test('attributes exclude files and relations', function () {
    $attributes = (new ProjectData(title: 'A', slug: 'a', summary: 'S'))->attributes();

    expect($attributes)->not->toHaveKeys(['cover', 'tag_ids', 'images']);
});
