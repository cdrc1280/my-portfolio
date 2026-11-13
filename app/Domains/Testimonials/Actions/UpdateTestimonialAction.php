<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Actions;

use App\Domains\Testimonials\Data\TestimonialData;
use App\Domains\Testimonials\Models\Testimonial;
use App\Shared\Actions\DeleteStoredFileAction;
use App\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Support\Facades\DB;

final class UpdateTestimonialAction
{
    public function __construct(
        private readonly StoreUploadedFileAction $storeFile,
        private readonly DeleteStoredFileAction $deleteFile,
    ) {}

    public function execute(Testimonial $testimonial, TestimonialData $data): Testimonial
    {
        $attributes = CreateTestimonialAction::columns($data);
        $previous = $testimonial->photo_path;

        if ($data->photo !== null) {
            $attributes['photo_path'] = $this->storeFile->execute($data->photo, 'testimonials');
        } elseif ($data->removePhoto) {
            $attributes['photo_path'] = null;
        }

        DB::transaction(fn (): bool => $testimonial->update($attributes));

        if (array_key_exists('photo_path', $attributes)) {
            $this->deleteFile->execute($previous);
        }

        return $testimonial;
    }
}
