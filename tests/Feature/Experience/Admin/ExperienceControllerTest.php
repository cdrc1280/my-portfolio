<?php

use App\Domains\Experience\Enums\EmploymentType;
use App\Domains\Experience\Models\Certificate;
use App\Domains\Experience\Models\Education;
use App\Domains\Experience\Models\Experience;
use Inertia\Testing\AssertableInertia as Assert;

function experiencePayload(array $overrides = []): array
{
    return array_merge([
        'company' => 'TESDA',
        'role' => 'Developer',
        'employment_type' => 'full_time',
        'started_on' => '2023-07',
        'ended_on' => '',
        'highlights' => ['One'],
        'technologies' => ['Laravel'],
    ], $overrides);
}

test('guests are redirected and non-admins forbidden', function () {
    $experience = Experience::factory()->create();

    $this->get(route('admin.experience.index'))->assertRedirect(route('login'));

    $this->actingAs(nonAdmin());
    $this->get(route('admin.experience.index'))->assertForbidden();
    $this->post(route('admin.experience.store'), experiencePayload())->assertForbidden();
    $this->put(route('admin.experience.update', $experience), experiencePayload())->assertForbidden();
    $this->delete(route('admin.experience.destroy', $experience))->assertForbidden();
    $this->post(route('admin.education.store'), [])->assertForbidden();
    $this->post(route('admin.certificates.store'), [])->assertForbidden();
});

test('index provides all three lists and employment types', function () {
    Experience::factory()->current()->create(['started_on' => '2023-07-01']);
    Education::factory()->create();
    Certificate::factory()->count(2)->create();

    $this->actingAs(admin())
        ->get(route('admin.experience.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/experience/index')
            ->has('experiences', 1)
            ->where('experiences.0.started_on', '2023-07')
            ->where('experiences.0.ended_on', null)
            ->where('experiences.0.is_current', true)
            ->has('education', 1)
            ->has('certificates', 2)
            ->where('employmentTypes', EmploymentType::options()));
});

test('store, update and destroy work experience', function () {
    $this->actingAs(admin());

    $this->post(route('admin.experience.store'), experiencePayload())->assertRedirect(route('admin.experience.index'));
    $experience = Experience::query()->sole();
    expect($experience->isCurrent())->toBeTrue();

    $this->put(route('admin.experience.update', $experience), experiencePayload(['ended_on' => '2025-01']))->assertRedirect();
    expect($experience->fresh()->ended_on->format('Y-m'))->toBe('2025-01');

    $this->delete(route('admin.experience.destroy', $experience))->assertRedirect();
    $this->assertModelMissing($experience);
});

test('experience validation', function (array $payload, string $error) {
    $this->actingAs(admin())
        ->post(route('admin.experience.store'), experiencePayload($payload))
        ->assertSessionHasErrors($error);
})->with([
    'missing company' => [['company' => ''], 'company'],
    'bad type' => [['employment_type' => 'volunteer'], 'employment_type'],
    'bad month' => [['started_on' => '07/2023'], 'started_on'],
    'ends before start' => [['ended_on' => '2020-01'], 'ended_on'],
    'bad url' => [['company_url' => 'nope'], 'company_url'],
]);

test('education and certificates CRUD', function () {
    $this->actingAs(admin());

    $this->post(route('admin.education.store'), [
        'institution' => 'ACLC', 'qualification' => 'BS', 'started_on' => '2018-06', 'ended_on' => '2022-06',
    ])->assertRedirect(route('admin.experience.index'));
    $education = Education::query()->sole();
    $this->put(route('admin.education.update', $education), [
        'institution' => 'ACLC College', 'qualification' => 'BS', 'started_on' => '2018-06',
    ])->assertRedirect();
    expect($education->fresh()->institution)->toBe('ACLC College');
    $this->delete(route('admin.education.destroy', $education))->assertRedirect();

    $this->post(route('admin.certificates.store'), ['name' => 'Cert', 'issuer' => 'DICT', 'issued_on' => '2025-06'])
        ->assertRedirect(route('admin.experience.index'));
    $certificate = Certificate::query()->sole();
    $this->post(route('admin.certificates.store'), ['name' => '', 'issuer' => '', 'issued_on' => 'x'])
        ->assertSessionHasErrors(['name', 'issued_on']);
    $this->delete(route('admin.certificates.destroy', $certificate))->assertRedirect();

    expect(Education::query()->count() + Certificate::query()->count())->toBe(0);
});

test('each list can be reordered', function () {
    [$a, $b] = Experience::factory()->count(2)->create()->all();
    [$c, $d] = Education::factory()->count(2)->create()->all();
    [$e, $f] = Certificate::factory()->count(2)->create()->all();
    $this->actingAs(admin());

    $this->post(route('admin.experience.reorder'), ['ids' => [$b->id, $a->id]])->assertRedirect();
    $this->post(route('admin.education.reorder'), ['ids' => [$d->id, $c->id]])->assertRedirect();
    $this->post(route('admin.certificates.reorder'), ['ids' => [$f->id, $e->id]])->assertRedirect();

    expect([$b->fresh()->position, $d->fresh()->position, $f->fresh()->position])->toBe([1, 1, 1]);
});

test('the employment type is optional', function () {
    $this->actingAs(admin())
        ->post(route('admin.experience.store'), experiencePayload(['employment_type' => '']))
        ->assertSessionHasNoErrors();

    expect(Experience::query()->sole()->employment_type)->toBeNull();
});
