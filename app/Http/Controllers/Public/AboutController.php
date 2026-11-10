<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Domain\Experience\Models\Certificate;
use App\Domain\Experience\Models\Education;
use App\Domain\Experience\Models\Experience;
use App\Domain\Profile\Actions\GetProfileAction;
use App\Domain\Skills\Models\SkillCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\AboutProfileResource;
use App\Http\Resources\CertificateResource;
use App\Http\Resources\EducationResource;
use App\Http\Resources\ExperienceResource;
use App\Http\Resources\SkillCategoryResource;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends Controller
{
    public function __invoke(GetProfileAction $getProfile): Response
    {
        return Inertia::render('public/about', [
            'profile' => AboutProfileResource::make($getProfile->execute()),
            'experiences' => ExperienceResource::collection(Experience::query()->ordered()->get()),
            'education' => EducationResource::collection(Education::query()->ordered()->get()),
            'certificates' => CertificateResource::collection(Certificate::query()->ordered()->get()),
            'skillCategories' => SkillCategoryResource::collection(SkillCategory::query()->with('skills')->ordered()->get()),
        ]);
    }
}
