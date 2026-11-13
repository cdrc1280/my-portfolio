<?php

use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Queries\NextProjectQuery;

test('next project follows position and wraps', function () {
    $a = Project::factory()->published()->create(['position' => 1]);
    $b = Project::factory()->published()->create(['position' => 2]);
    Project::factory()->create(['position' => 3]);

    $next = app(NextProjectQuery::class);

    expect($next->execute($a)?->id)->toBe($b->id)
        ->and($next->execute($b)?->id)->toBe($a->id);
});
