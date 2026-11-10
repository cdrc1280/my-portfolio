<?php

use App\Domain\Projects\Actions\ReorderProjectsAction;
use App\Domain\Projects\Models\Project;

test('persists the given order', function () {
    [$a, $b, $c] = Project::factory()->count(3)->create()->all();

    app(ReorderProjectsAction::class)->execute([$c->id, $a->id, $b->id]);

    expect(Project::query()->ordered()->pluck('id')->all())->toBe([$c->id, $a->id, $b->id]);
});

test('rejects unknown ids without changing anything', function () {
    $project = Project::factory()->create(['position' => 5]);

    expect(fn () => app(ReorderProjectsAction::class)->execute([$project->id, 999]))
        ->toThrow(InvalidArgumentException::class);

    expect($project->fresh()->position)->toBe(5);
});
