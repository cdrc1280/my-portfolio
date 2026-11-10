<?php

use App\Domain\Projects\Enums\ProjectStatus;

test('exposes select options for every case', function () {
    expect(ProjectStatus::options())->toBe([
        ['value' => 'draft', 'label' => 'Draft'],
        ['value' => 'published', 'label' => 'Published'],
    ]);
});
