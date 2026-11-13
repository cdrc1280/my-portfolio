<?php

declare(strict_types=1);

namespace App\Domains\Testimonials\Actions;

use App\Domains\Testimonials\Data\TestimonialData;
use App\Domains\Testimonials\Models\Testimonial;
use App\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Support\Facades\DB;

final class CreateTestimonialAction
{
    public function __construct(
        private readonly StoreUploadedFileAction $storeFile,
    ) {}

    public function execute(TestimonialData $data): Testimonial
    {
        return DB::transaction(fn (): Testimonial => Testimonial::query()->create([
            ...self::columns($data),
            'photo_path' => $data->photo !== null ? $this->storeFile->execute($data->photo, 'testimonials') : null,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public static function columns(TestimonialData $data): array
    {
        return [
            'author_name' => $data->authorName,
            'quote' => $data->quote,
            'author_role' => $data->authorRole,
            'company' => $data->company,
            'project_id' => $data->projectId,
            'has_permission' => $data->hasPermission,
            'is_featured' => $data->isFeatured,
        ];
    }
}
