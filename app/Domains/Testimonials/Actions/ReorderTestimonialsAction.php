<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Actions;

use App\Domains\Testimonials\Models\Testimonial;
use App\Shared\Actions\ReorderAction;

final class ReorderTestimonialsAction
{
    public function __construct(
        private readonly ReorderAction $reorder,
    ) {}

    /**
     * @param  list<int>  $orderedIds
     */
    public function execute(array $orderedIds): void
    {
        $this->reorder->execute(Testimonial::class, $orderedIds);
    }
}
