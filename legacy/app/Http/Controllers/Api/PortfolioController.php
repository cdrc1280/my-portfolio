<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Certificate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\ProfileMedia;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\SkillCategory;
use App\Models\SocialLink;
use App\Models\Testimonial;
use App\Models\TimelineEvent;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    public function index(): JsonResponse
    {
        $settings = SiteSetting::all()->pluck('value', 'key');

        return response()->json([
            'settings' => $settings,
            'project_categories' => ProjectCategory::with('projects')->get(),
            'projects' => Project::with('category')->orderBy('order')->get(),
            'skill_categories' => SkillCategory::with('skills')->get(),
            'skills' => Skill::with('category')->orderBy('order')->get(),
            'experiences' => Experience::orderBy('order')->get(),
            'education' => Education::orderBy('order')->get(),
            'certificates' => Certificate::orderBy('order')->get(),
            'services' => Service::orderBy('order')->get(),
            'testimonials' => Testimonial::where('is_featured', true)->get(),
            'faqs' => Faq::orderBy('order')->get(),
            'timeline' => TimelineEvent::orderBy('order')->get(),
            'blogs' => Blog::with('category')->where('status', 'published')->latest('published_at')->get(),
            'social_links' => SocialLink::orderBy('order')->get(),
            'profile_medias' => ProfileMedia::where('is_active', true)->orderBy('order')->get(),
        ]);
    }
}
