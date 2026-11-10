<?php

use App\Domain\Experience\Actions\ReorderCareerHistoryAction;
use App\Domain\Experience\Models\Certificate;
use App\Domain\Experience\Models\Experience;

test('reorders the requested list only', function () {
    [$a, $b] = Experience::factory()->count(2)->create()->all();
    $certificate = Certificate::factory()->create(['position' => 7]);

    app(ReorderCareerHistoryAction::class)->execute(Experience::class, [$b->id, $a->id]);

    expect(Experience::query()->ordered()->pluck('id')->all())->toBe([$b->id, $a->id])
        ->and($certificate->fresh()->position)->toBe(7);
});
