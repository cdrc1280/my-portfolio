<?php

use App\Domains\Experience\Actions\DeleteExperienceAction;
use App\Domains\Experience\Models\Experience;

test('deletes a role', function () {
    $experience = Experience::factory()->create();

    app(DeleteExperienceAction::class)->execute($experience);

    $this->assertModelMissing($experience);
});
