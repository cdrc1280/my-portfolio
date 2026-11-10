<?php

use App\Domain\Projects\Models\Project;
use App\Domain\Tags\Actions\DeleteTagAction;
use App\Domain\Tags\Models\Tag;

test('deletes a tag and detaches it from projects', function () {
    $tag = Tag::factory()->create();
    $project = Project::factory()->create();
    $project->tags()->attach($tag);

    app(DeleteTagAction::class)->execute($tag);

    $this->assertModelMissing($tag);
    $this->assertDatabaseCount('taggables', 0);
    $this->assertModelExists($project);
});
