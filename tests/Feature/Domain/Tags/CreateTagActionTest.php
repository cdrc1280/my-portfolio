<?php

use App\Domain\Tags\Actions\CreateTagAction;
use App\Domain\Tags\DataTransferObjects\TagData;

test('creates a tag', function () {
    $tag = app(CreateTagAction::class)->execute(TagData::fromArray(['name' => 'Laravel']));

    expect($tag->name)->toBe('Laravel')->and($tag->slug)->toBe('laravel');
    $this->assertDatabaseHas('tags', ['slug' => 'laravel']);
});

test('derives the slug from the name when none is given', function () {
    $tag = app(CreateTagAction::class)->execute(TagData::fromArray(['name' => '  Tailwind CSS  ', 'slug' => '']));

    expect($tag->name)->toBe('Tailwind CSS')->and($tag->slug)->toBe('tailwind-css');
});
