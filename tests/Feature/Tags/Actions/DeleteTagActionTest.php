<?php

use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Actions\DeleteTagAction;
use App\Domains\Tags\Models\Tag;

test('deletes a tag and detaches it from projects', function () {
    $tag = Tag::factory()->create();
    $project = Project::factory()->create();
    $project->tags()->attach($tag);

    app(DeleteTagAction::class)->execute($tag);

    $this->assertModelMissing($tag);
    $this->assertDatabaseCount('taggables', 0);
    $this->assertModelExists($project);
});
