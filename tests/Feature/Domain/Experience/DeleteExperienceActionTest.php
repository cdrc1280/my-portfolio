<?php

use App\Domain\Experience\Actions\DeleteExperienceAction;
use App\Domain\Experience\Models\Experience;

test('deletes a role', function () {
    $experience = Experience::factory()->create();

    app(DeleteExperienceAction::class)->execute($experience);

    $this->assertModelMissing($experience);
});
