<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Controllers\Admin;

use App\Domains\Projects\Queries\ProjectQueries;
use App\Domains\Testimonials\Actions\CreateTestimonialAction;
use App\Domains\Testimonials\Actions\DeleteTestimonialAction;
use App\Domains\Testimonials\Actions\ReorderTestimonialsAction;
use App\Domains\Testimonials\Actions\UpdateTestimonialAction;
use App\Domains\Testimonials\Data\TestimonialData;
use App\Domains\Testimonials\Models\Testimonial;
use App\Domains\Testimonials\Queries\TestimonialQueries;
use App\Http\Controllers\Controller;
use App\Shared\Data\ReorderData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class TestimonialController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,'.Testimonial::class, only: ['index']),
            new Middleware('can:create,'.Testimonial::class, only: ['store']),
            new Middleware('can:update,testimonial', only: ['update']),
            new Middleware('can:reorder,'.Testimonial::class, only: ['reorder']),
            new Middleware('can:delete,testimonial', only: ['destroy']),
        ];
    }

    public function index(TestimonialQueries $queries, ProjectQueries $projects): Response
    {
        return Inertia::render('admin/testimonials/index', [
            'testimonials' => $queries->forAdmin(),
            'projects' => $projects->allAsOptions(),
        ]);
    }

    public function store(TestimonialData $data, CreateTestimonialAction $action): RedirectResponse
    {
        $action->execute($data);

        $this->toast('Testimonial added.');

        return to_route('admin.testimonials.index');
    }

    public function update(TestimonialData $data, Testimonial $testimonial, UpdateTestimonialAction $action): RedirectResponse
    {
        $action->execute($testimonial, $data);

        $this->toast('Testimonial updated.');

        return to_route('admin.testimonials.index');
    }

    public function destroy(Testimonial $testimonial, DeleteTestimonialAction $action): RedirectResponse
    {
        $action->execute($testimonial);

        $this->toast('Testimonial deleted.');

        return to_route('admin.testimonials.index');
    }

    public function reorder(ReorderData $data, ReorderTestimonialsAction $action): RedirectResponse
    {
        $action->execute($data->ids);

        $this->toast('Order saved.');

        return to_route('admin.testimonials.index');
    }
}
