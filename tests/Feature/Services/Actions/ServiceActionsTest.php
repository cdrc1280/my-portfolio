<?php

use App\Domains\Services\Actions\CreateServiceAction;
use App\Domains\Services\Actions\DeleteServiceAction;
use App\Domains\Services\Actions\ReorderServicesAction;
use App\Domains\Services\Actions\UpdateServiceAction;
use App\Domains\Services\Data\ServiceData;
use App\Domains\Services\Models\Service;

describe('services', function () {
    test('create appends, update trims and nulls empty icon, delete removes', function () {
        Service::factory()->create();

        $service = app(CreateServiceAction::class)->execute(ServiceData::from([
            'title' => ' Laravel apps ', 'description' => ' New builds. ', 'icon' => 'server',
        ]));

        expect($service)->title->toBe('Laravel apps')->description->toBe('New builds.')->position->toBe(2);

        app(UpdateServiceAction::class)->execute($service, ServiceData::from([
            'title' => 'Laravel', 'description' => 'Apps', 'icon' => '  ',
        ]));
        expect($service->fresh()->icon)->toBeNull();

        app(DeleteServiceAction::class)->execute($service);
        $this->assertModelMissing($service);
    });

    test('reorder sets positions', function () {
        [$a, $b] = Service::factory()->count(2)->create();

        app(ReorderServicesAction::class)->execute([$b->id, $a->id]);

        expect($b->fresh()->position)->toBe(1)->and($a->fresh()->position)->toBe(2);
    });
});
