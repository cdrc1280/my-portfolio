<?php

declare(strict_types=1);

namespace App\Domains\Profile\Controllers\Public;

use App\Domains\Profile\Actions\GetProfileAction;
use App\Http\Controllers\Controller;
use App\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeController extends Controller
{
    public function __invoke(GetProfileAction $getProfile): StreamedResponse
    {
        $profile = $getProfile->execute();
        $disk = Storage::disk(StoreUploadedFileAction::disk());

        abort_if($profile->resume_path === null || ! $disk->exists($profile->resume_path), 404);

        return $disk->download($profile->resume_path, Str::slug($profile->name).'-resume.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
