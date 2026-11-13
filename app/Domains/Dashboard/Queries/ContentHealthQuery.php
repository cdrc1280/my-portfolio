<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Queries;

use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Projects\Models\Project;
use App\Domains\Testimonials\Models\Testimonial;
use Illuminate\Database\Eloquent\Builder;

/**
 * A checklist of what would make the portfolio more convincing, for the dashboard. Each check links
 * to the admin page that fixes it.
 */
final class ContentHealthQuery
{
    private const RESUME_STALE_DAYS = 180;

    public function __construct(
        private readonly GetProfileAction $getProfile,
    ) {}

    /**
     * @return list<array{key: string, label: string, ok: bool, detail: string, href: string}>
     */
    public function execute(): array
    {
        $profile = $this->getProfile->execute();

        $withoutScreenshots = Project::query()->published()
            ->whereNull('cover_image')
            ->whereDoesntHave('images')
            ->pluck('title');

        $thinCaseStudies = Project::query()->published()
            ->where(fn (Builder $query) => $query->whereNull('problem')->orWhereNull('results')->orWhereNull('architecture'))
            ->pluck('title');

        $featured = Project::query()->published()->featured()->count();
        $testimonials = Testimonial::query()->publishable()->count();
        $resumeAge = $profile->resume_updated_at?->diffInDays(now());

        return [
            [
                'key' => 'screenshots',
                'label' => 'Every published project has screenshots',
                'ok' => $withoutScreenshots->isEmpty(),
                'detail' => $withoutScreenshots->isEmpty() ? 'All set' : 'Missing: '.$withoutScreenshots->implode(', '),
                'href' => route('admin.projects.index', absolute: false),
            ],
            [
                'key' => 'case_studies',
                'label' => 'Case studies cover problem, architecture and results',
                'ok' => $thinCaseStudies->isEmpty(),
                'detail' => $thinCaseStudies->isEmpty() ? 'All set' : 'Incomplete: '.$thinCaseStudies->implode(', '),
                'href' => route('admin.projects.index', absolute: false),
            ],
            [
                'key' => 'featured',
                'label' => 'Three featured projects on the home page',
                'ok' => $featured >= 3,
                'detail' => "{$featured} featured",
                'href' => route('admin.projects.index', absolute: false),
            ],
            [
                'key' => 'testimonials',
                'label' => 'At least one published testimonial',
                'ok' => $testimonials > 0,
                'detail' => $testimonials > 0 ? "{$testimonials} published" : 'None yet: ask a past client',
                'href' => route('admin.testimonials.index', absolute: false),
            ],
            [
                'key' => 'booking',
                'label' => 'Booking link for “Book a call”',
                'ok' => $profile->booking_url !== null,
                'detail' => $profile->booking_url !== null ? 'Connected' : 'Add your Calendly link in Settings',
                'href' => route('admin.profile.edit', absolute: false),
            ],
            [
                'key' => 'resume',
                'label' => 'Up-to-date résumé',
                'ok' => $resumeAge !== null && $resumeAge <= self::RESUME_STALE_DAYS,
                'detail' => match (true) {
                    $profile->resume_path === null => 'No résumé uploaded',
                    $resumeAge === null => 'Uploaded',
                    default => 'Updated '.(int) $resumeAge.' days ago',
                },
                'href' => route('admin.profile.edit', absolute: false),
            ],
            [
                'key' => 'seo',
                'label' => 'Default SEO title, description and social image',
                'ok' => $profile->seo_title !== null && $profile->seo_description !== null && $profile->og_image_path !== null,
                'detail' => $profile->og_image_path === null ? 'Social image missing' : 'All set',
                'href' => route('admin.profile.edit', absolute: false),
            ],
        ];
    }
}
