<?php

use App\Domains\Contact\Enums\BudgetRange;
use App\Domains\Contact\Enums\ProjectType;
use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Dashboard\Queries\ContentHealthQuery;
use App\Domains\Dashboard\Queries\LeadStatsQuery;
use App\Domains\Profile\Models\Profile;
use App\Domains\Projects\Models\Project;
use App\Domains\Testimonials\Models\Testimonial;

test('lead stats count unread, this week vs last week and the budget/type mix', function () {
    $this->travelTo(now()->startOfWeek()->addDays(2));
    ContactMessage::factory()->count(2)->create(['budget' => BudgetRange::From1kTo3k, 'project_type' => ProjectType::NewBuild]);
    ContactMessage::factory()->read()->create(['budget' => BudgetRange::Over8k, 'project_type' => ProjectType::NewBuild, 'created_at' => now()->subWeek()]);
    ContactMessage::factory()->create(['budget' => null, 'project_type' => null, 'created_at' => now()->subDays(200)]);

    $stats = app(LeadStatsQuery::class)->execute();

    expect($stats)
        ->unread->toBe(3)
        ->this_week->toBe(2)
        ->last_week->toBe(1)
        ->budgets->toBe([
            ['label' => '$1,000 – $3,000', 'count' => 2],
            ['label' => '$8,000+', 'count' => 1],
        ])
        ->project_types->toBe([['label' => 'New web application', 'count' => 3]]);
});

test('content health flags what is missing and clears when fixed', function () {
    Profile::factory()->create(['booking_url' => null]);
    Project::factory()->featured()->create(['title' => 'Bare', 'cover_image' => null, 'architecture' => null]);

    $checks = collect(app(ContentHealthQuery::class)->execute())->keyBy('key');

    expect($checks['screenshots']['ok'])->toBeFalse()
        ->and($checks['screenshots']['detail'])->toContain('Bare')
        ->and($checks['case_studies']['ok'])->toBeFalse()
        ->and($checks['featured']['ok'])->toBeFalse()
        ->and($checks['testimonials']['ok'])->toBeFalse()
        ->and($checks['booking']['ok'])->toBeFalse()
        ->and($checks['resume']['detail'])->toBe('No résumé uploaded');

    Profile::query()->update(['booking_url' => 'https://calendly.com/me']);
    Testimonial::factory()->publishable()->create();

    $checks = collect(app(ContentHealthQuery::class)->execute())->keyBy('key');
    expect($checks['booking']['ok'])->toBeTrue()->and($checks['testimonials']['ok'])->toBeTrue();
});
