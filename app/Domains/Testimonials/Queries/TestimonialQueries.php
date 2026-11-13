<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Queries;

use App\Domains\Projects\Models\Project;
use App\Domains\Testimonials\Models\Testimonial;
use App\Domains\Testimonials\Resources\AdminTestimonialResource;
use App\Domains\Testimonials\Resources\TestimonialResource;

/**
 * Read side of the Testimonials module.
 */
final class TestimonialQueries
{
    /**
     * @return list<AdminTestimonialResource>
     */
    public function forAdmin(): array
    {
        return array_values(Testimonial::query()->with('project')->ordered()->get()
            ->map(fn (Testimonial $testimonial): AdminTestimonialResource => AdminTestimonialResource::fromModel($testimonial))
            ->all());
    }

    /**
     * Up to three featured quotes the client approved, for the home page.
     *
     * @return list<TestimonialResource>
     */
    public function featuredForHome(): array
    {
        return array_values(Testimonial::query()->publishable()->featured()->with('project')->ordered()->limit(3)->get()
            ->map(fn (Testimonial $testimonial): TestimonialResource => TestimonialResource::fromModel($testimonial))
            ->all());
    }

    /**
     * The first approved quote linked to a project, for its case study.
     */
    public function forProject(Project $project): ?TestimonialResource
    {
        $testimonial = $project->testimonials()->publishable()->ordered()->first();

        return $testimonial ? TestimonialResource::fromModel($testimonial) : null;
    }
}
