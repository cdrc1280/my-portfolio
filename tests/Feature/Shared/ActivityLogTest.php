<?php

use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Tags\Models\Tag;
use Spatie\Activitylog\Models\Activity;

test('changes to a model are logged with old and new values', function () {
    $tag = Tag::factory()->create(['name' => 'Vue']);
    $tag->update(['name' => 'Vue.js']);

    $activity = Activity::query()->where('subject_type', $tag->getMorphClass())->latest('id')->first();

    expect($activity)->not->toBeNull()
        ->and($activity->event)->toBe('updated')
        ->and($activity->attribute_changes?->get('attributes')['name'] ?? null)->toBe('Vue.js')
        ->and($activity->attribute_changes?->get('old')['name'] ?? null)->toBe('Vue');
});

test('saves that change nothing are not logged', function () {
    $tag = Tag::factory()->create();
    $count = Activity::query()->count();

    $tag->update(['name' => $tag->name]);

    expect(Activity::query()->count())->toBe($count);
});

test('hashed IPs are never written to the log', function () {
    ContactMessage::factory()->create(['ip_hash' => str_repeat('a', 64)]);

    expect(Activity::query()->latest('id')->first()?->attribute_changes?->get('attributes') ?? [])->not->toHaveKey('ip_hash');
});
