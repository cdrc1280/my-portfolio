<?php

use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use App\Domains\Tags\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(fn () => Storage::fake('public'));

function validProjectPayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'Document Tracking System',
        'summary' => 'Tracks official documents across departments.',
        'status' => 'published',
        'is_featured' => true,
        'problem' => 'Memos moved on paper.',
        'architecture_tree' => "app/\n  Domain/  # logic",
        'metrics' => [['label' => 'Turnaround', 'value' => '-3 days'], ['label' => '', 'value' => '']],
        'started_on' => '2023-08',
        'ended_on' => '2023-12',
    ], $overrides);
}

describe('access control', function () {
    test('guests are redirected to login', function (string $method, string $uri) {
        $this->call($method, $uri)->assertRedirect(route('login'));
    })->with([
        ['GET', '/admin/projects'],
        ['GET', '/admin/projects/create'],
        ['POST', '/admin/projects'],
        ['POST', '/admin/projects/reorder'],
    ]);

    test('non-admins are forbidden', function () {
        $project = Project::factory()->create();
        $this->actingAs(nonAdmin());

        $this->get(route('admin.projects.index'))->assertForbidden();
        $this->post(route('admin.projects.store'), validProjectPayload())->assertForbidden();
        $this->put(route('admin.projects.update', $project), validProjectPayload())->assertForbidden();
        $this->delete(route('admin.projects.destroy', $project))->assertForbidden();

        $this->assertDatabaseCount('projects', 1);
    });
});

test('index lists every project in order with tags', function () {
    $second = Project::factory()->published()->create(['position' => 2]);
    $first = Project::factory()->create(['position' => 1]);
    $first->tags()->attach(Tag::factory()->create());

    $this->actingAs(admin())
        ->get(route('admin.projects.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/projects/index')
            ->has('projects', 2)
            ->where('projects.0.id', $first->id)
            ->where('projects.0.status', 'draft')
            ->has('projects.0.tags', 1)
            ->where('projects.1.id', $second->id));
});

test('create page provides tags and statuses', function () {
    Tag::factory()->count(2)->create();

    $this->actingAs(admin())
        ->get(route('admin.projects.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/projects/create')
            ->has('tags', 2)
            ->where('statuses', ProjectStatus::options()));
});

test('store creates a project and redirects to edit', function () {
    $tag = Tag::factory()->create();

    $response = $this->actingAs(admin())->post(route('admin.projects.store'), validProjectPayload([
        'tag_ids' => [$tag->id],
        'cover' => UploadedFile::fake()->image('cover.jpg'),
    ]));

    $project = Project::query()->sole();
    $response->assertRedirect(route('admin.projects.edit', $project))->assertInertiaFlash('toast.type', 'success');

    expect($project)
        ->slug->toBe('document-tracking-system')
        ->status->toBe(ProjectStatus::Published)
        ->problem->toBe('Memos moved on paper.')
        ->metrics->toBe([['label' => 'Turnaround', 'value' => '-3 days']])
        ->architecture_tree->toBe([['name' => 'app', 'children' => [['name' => 'Domain', 'note' => 'logic', 'children' => []]]]])
        ->and($project->started_on?->format('Y-m'))->toBe('2023-08')
        ->and($project->tags->modelKeys())->toBe([$tag->id]);
    Storage::disk('public')->assertExists($project->cover_image);
});

test('store validates input', function (array $payload, array $errors) {
    $this->actingAs(admin())
        ->post(route('admin.projects.store'), validProjectPayload($payload))
        ->assertSessionHasErrors($errors);

    $this->assertDatabaseCount('projects', 0);
})->with([
    'missing title' => [['title' => ''], ['title']],
    'missing summary' => [['summary' => ''], ['summary']],
    'unknown status' => [['status' => 'archived'], ['status']],
    'bad url' => [['live_url' => 'not-a-url', 'repo_url' => 'ftp://x.test'], ['live_url', 'repo_url']],
    'unknown tag' => [['tag_ids' => [999]], ['tag_ids.0']],
    'end before start' => [['started_on' => '2024-05', 'ended_on' => '2024-01'], ['ended_on']],
    'bad month' => [['started_on' => '2024-13-01'], ['started_on']],
    'metric without value' => [['metrics' => [['label' => 'Speed', 'value' => '']]], ['metrics.0.value']],
    'too many metrics' => [['metrics' => array_fill(0, 9, ['label' => 'A', 'value' => '1'])], ['metrics']],
    'bad demo url' => [['demo_url' => 'javascript:alert(1)'], ['demo_url']],
    'non-image cover' => [['cover' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf')], ['cover']],
    'oversized image' => [['images' => [UploadedFile::fake()->image('big.jpg')->size(5000)]], ['images.0']],
]);

test('store rejects a duplicate slug', function () {
    Project::factory()->create(['slug' => 'document-tracking-system']);

    $this->actingAs(admin())
        ->post(route('admin.projects.store'), validProjectPayload())
        ->assertSessionHasErrors('slug');
});

test('edit page exposes the editable shape', function () {
    $project = Project::factory()->create([
        'decisions' => '# Raw markdown',
        'architecture_tree' => [['name' => 'app', 'children' => [['name' => 'Models']]]],
    ]);

    $this->actingAs(admin())
        ->get(route('admin.projects.edit', $project))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/projects/edit')
            ->where('project.id', $project->id)
            ->where('project.decisions', '# Raw markdown')
            ->where('project.architecture_tree', "app/\n  Models")
            ->has('project.images')
            ->has('tags')
            ->has('statuses'));
});

test('update saves changes and keeps its own slug', function () {
    $project = Project::factory()->create(['slug' => 'keep-me']);

    $this->actingAs(admin())
        ->put(route('admin.projects.update', $project), validProjectPayload(['slug' => 'keep-me', 'title' => 'New title']))
        ->assertRedirect(route('admin.projects.edit', $project));

    expect($project->fresh())->title->toBe('New title')->slug->toBe('keep-me');
});

test('update replaces the diagram and removes the old file', function () {
    $project = Project::factory()->create(['diagram_path' => 'projects/diagrams/old.png']);
    Storage::disk('public')->put('projects/diagrams/old.png', 'x');

    $this->actingAs(admin())
        ->put(route('admin.projects.update', $project), validProjectPayload([
            'slug' => $project->slug,
            'diagram' => UploadedFile::fake()->image('diagram.png'),
        ]))
        ->assertRedirect();

    $project->refresh();
    expect($project->diagram_path)->not->toBe('projects/diagrams/old.png');
    Storage::disk('public')->assertExists($project->diagram_path);
    Storage::disk('public')->assertMissing('projects/diagrams/old.png');
});

test('destroy deletes the project', function () {
    $project = Project::factory()->create();

    $this->actingAs(admin())
        ->delete(route('admin.projects.destroy', $project))
        ->assertRedirect(route('admin.projects.index'));

    $this->assertModelMissing($project);
});

test('reorder persists positions', function () {
    [$a, $b] = Project::factory()->count(2)->create()->all();

    $this->actingAs(admin())
        ->post(route('admin.projects.reorder'), ['ids' => [$b->id, $a->id]])
        ->assertRedirect(route('admin.projects.index'));

    expect($b->fresh()->position)->toBe(1)->and($a->fresh()->position)->toBe(2);
});

test('reorder validates ids', function () {
    $this->actingAs(admin())
        ->post(route('admin.projects.reorder'), ['ids' => ['x']])
        ->assertSessionHasErrors('ids.0');
});
