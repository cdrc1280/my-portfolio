<?php

use App\Domains\WorkApproach\Actions\CreateWorkApproachAction;
use App\Domains\WorkApproach\Actions\DeleteWorkApproachAction;
use App\Domains\WorkApproach\Actions\ReorderWorkApproachesAction;
use App\Domains\WorkApproach\Actions\UpdateWorkApproachAction;
use App\Domains\WorkApproach\Data\WorkApproachData;
use App\Domains\WorkApproach\Models\WorkApproach;

describe('work approaches', function () {
    test('create, update and delete', function () {
        $approach = app(CreateWorkApproachAction::class)->execute(WorkApproachData::from([
            'title' => 'Tested by default', 'description' => 'Pest.', 'snippet' => '', 'icon' => 'flask-conical',
        ]));
        expect($approach->snippet)->toBeNull();

        app(UpdateWorkApproachAction::class)->execute($approach, WorkApproachData::from([
            'title' => 'Tested', 'description' => 'Pest.', 'snippet' => '$ make ci',
        ]));
        expect($approach->fresh())->title->toBe('Tested')->snippet->toBe('$ make ci')->icon->toBeNull();

        app(DeleteWorkApproachAction::class)->execute($approach);
        $this->assertModelMissing($approach);
    });

    test('reorder sets positions', function () {
        [$a, $b] = WorkApproach::factory()->count(2)->create();

        app(ReorderWorkApproachesAction::class)->execute([$b->id, $a->id]);

        expect($b->fresh()->position)->toBe(1);
    });
});
