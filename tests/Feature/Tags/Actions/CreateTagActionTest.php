<?php

use App\Domains\Tags\Actions\CreateTagAction;
use App\Domains\Tags\Data\TagData;

test('creates a tag', function () {
    $tag = app(CreateTagAction::class)->execute(TagData::from(['name' => 'Laravel']));

    expect($tag->name)->toBe('Laravel')->and($tag->slug)->toBe('laravel');
    $this->assertDatabaseHas('tags', ['slug' => 'laravel']);
});

test('derives the slug from the name when none is given', function () {
    $tag = app(CreateTagAction::class)->execute(TagData::from(['name' => '  Tailwind CSS  ', 'slug' => '']));

    expect($tag->name)->toBe('Tailwind CSS')->and($tag->slug)->toBe('tailwind-css');
});
