<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Actions;

use App\Domains\Testimonials\Models\Testimonial;
use App\Shared\Actions\DeleteStoredFileAction;

final class DeleteTestimonialAction
{
    public function __construct(
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Testimonial $testimonial): void
    {
        $path = $testimonial->photo_path;
        $testimonial->delete();
        $this->deleteFile->execute($path);
    }
}
