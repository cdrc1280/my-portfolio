<?php

namespace Database\Seeders;

use App\Domain\Blog\Enums\PostStatus;
use App\Domain\Blog\Models\Post;
use App\Domain\Contact\Models\ContactMessage;
use App\Domain\Experience\Enums\EmploymentType;
use App\Domain\Experience\Models\Certificate;
use App\Domain\Experience\Models\Education;
use App\Domain\Experience\Models\Experience;
use App\Domain\Profile\Enums\SocialPlatform;
use App\Domain\Profile\Events\ProfileChanged;
use App\Domain\Profile\Models\Profile;
use App\Domain\Profile\Models\SocialLink;
use App\Domain\Projects\Enums\ProjectStatus;
use App\Domain\Projects\Models\Project;
use App\Domain\Skills\Models\SkillCategory;
use App\Domain\Tags\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Realistic sample content, ported from the previous version of the site.
 * Images are remote placeholders or files in public/, so nothing has to
 * exist on the media disk.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProfile();
        $this->seedSkills();
        $this->seedProjects();
        $this->seedExperience();
        $this->seedPosts();
        $this->seedMessages();

        ProfileChanged::dispatch();
    }

    private function seedProfile(): void
    {
        Profile::query()->updateOrCreate([], [
            'name' => 'Cedric James Leala',
            'headline' => 'Full-stack developer',
            'tagline' => 'I build and maintain production web systems end to end — schema, API and UI — with Laravel, Filament, Vue and React, using AI-assisted workflows to ship fast without sacrificing maintainability.',
            'bio' => <<<'MD'
                I'm a full-stack developer with three years of experience shipping and maintaining **six concurrently running production systems** for TESDA, owning each one end to end: database schema, API and user interface.

                My core stack is **Laravel, Filament, Vue.js and MySQL**, with additional front-end experience in **React, TypeScript and Inertia.js** from freelance work.

                I use Claude Code and other AI coding assistants as part of a daily build–test–ship workflow. That lets me move quickly on scaffolding and debugging while I spend my attention on architecture, data modelling and the details users actually notice.

                I'm comfortable taking a scoped problem from requirements to working, production-ready software on my own — and equally happy working inside a team's conventions.
                MD,
            'location' => 'Sitio Gulod Muzon, Taytay, Rizal 1920',
            'email' => 'cedricjamesleala128@gmail.com',
            'phone' => '0938-854-2688',
            'availability' => 'Available for full-stack & Laravel work',
            'avatar_path' => '/images/cedric-profile.jpg',
        ]);

        $links = [
            [SocialPlatform::GitHub, 'https://github.com/cdrc1280'],
            [SocialPlatform::Email, 'mailto:cedricjamesleala128@gmail.com'],
        ];

        foreach ($links as $position => [$platform, $url]) {
            SocialLink::query()->updateOrCreate(['platform' => $platform], ['url' => $url, 'position' => $position + 1]);
        }
    }

    private function seedSkills(): void
    {
        $groups = [
            'Core stack' => ['Laravel', 'Laravel Filament', 'Vue.js', 'Tailwind CSS', 'JavaScript', 'MySQL', 'HTML, CSS & SCSS', 'Git & GitHub'],
            'Also worked with' => ['React', 'TypeScript', 'Inertia.js', 'REST APIs', 'Livewire', 'Alpine.js', 'Bootstrap', 'CodeIgniter'],
            'AI-assisted development' => ['Claude Code', 'ChatGPT', 'Gemini'],
        ];

        foreach (array_keys($groups) as $position => $name) {
            $category = SkillCategory::query()->updateOrCreate(['name' => $name], ['position' => $position + 1]);

            foreach ($groups[$name] as $skillPosition => $skill) {
                $category->skills()->updateOrCreate(['name' => $skill], ['position' => $skillPosition + 1]);
            }
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            [
                'title' => 'TESDA Profiling MIS',
                'summary' => 'National profiling system with multi-agency access, scholarship management and real-time analytics.',
                'role' => 'Full-stack developer',
                'period' => '2023 – 2026',
                'live_url' => 'https://mis.tesda-clgeo.com',
                'cover' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1200&auto=format&fit=crop',
                'gallery' => [
                    'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1200&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?q=80&w=1200&auto=format&fit=crop',
                ],
                'tags' => ['Laravel', 'Filament', 'Vue.js', 'MySQL', 'Tailwind CSS'],
                'highlights' => [
                    'Multi-agency role-based access control',
                    'Real-time scholarship profiling and analytics',
                    'MySQL indexing for sub-second profiling queries',
                    'Filament administrative dashboards',
                ],
                'body' => [
                    'Overview' => 'Built and maintained with Laravel, Filament and Vue.js. The system automates database operations for national scholarship programmes, gates access per agency and role, and keeps profiling queries fast across a large and growing dataset.',
                    'Architecture' => 'A Laravel back end serves the Vue.js front end, while Filament powers the administrative side for staff who manage records day to day.',
                    'Challenge' => 'Aggregating data from multiple agencies while keeping profiling queries under a second across thousands of records.',
                    'Approach' => 'Designed targeted MySQL indexes and moved heavy work into chunked query pipelines, so reports stay responsive as data grows.',
                    'What I learned' => 'Pairing Filament with AI-assisted scaffolding dramatically shortens the time it takes to deliver dependable internal dashboards.',
                ],
                'featured' => true,
            ],
            [
                'title' => 'CBTVETO Landing Site & Directory',
                'summary' => 'Public LMS portal, TESDA directory and automated document verification.',
                'role' => 'Full-stack developer',
                'period' => '2024',
                'live_url' => 'https://tesda-clgeo.com',
                'cover' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop',
                'gallery' => ['https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=1200&auto=format&fit=crop'],
                'tags' => ['Laravel', 'Vue.js', 'Tailwind CSS', 'MySQL', 'REST APIs'],
                'highlights' => ['Public institutional directory', 'Certificate verification engine', 'Responsive Vue.js + Tailwind UI'],
                'body' => [
                    'Overview' => 'A public-facing directory and verification platform serving TESDA institutions, trainers and students, including real-time certificate validation.',
                ],
                'featured' => true,
            ],
            [
                'title' => 'National Directory for CTECs',
                'summary' => "Trainers' profiles, association management and a national achievement tracker.",
                'role' => 'Full-stack developer',
                'period' => '2024',
                'live_url' => 'https://ctec.tesda-clgeo.com',
                'cover' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1200&auto=format&fit=crop',
                'tags' => ['Laravel', 'Filament', 'Vue.js', 'MySQL'],
                'highlights' => ['Regional trainer profiles', 'Association memberships', 'Achievement milestones'],
                'body' => [
                    'Overview' => 'A centralised directory for community technical education centre (CTEC) trainers, their association memberships and achievement milestones across regions.',
                ],
                'featured' => true,
            ],
            [
                'title' => 'CBTVET Learning Management System',
                'summary' => 'Student enrolment, courses and learning-materials portal.',
                'role' => 'Full-stack developer',
                'period' => '2023 – 2024',
                'live_url' => 'https://lms.cbtveto.com',
                'cover' => 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?q=80&w=1200&auto=format&fit=crop',
                'tags' => ['Laravel', 'Vue.js', 'Tailwind CSS', 'MySQL'],
                'highlights' => ['Student registration', 'Course uploads', 'Assignment tracking and grading'],
                'body' => [
                    'Overview' => 'A full learning management system covering student registration, course uploads, assignment tracking and grade recording.',
                ],
                'featured' => true,
            ],
            [
                'title' => 'Document Tracking System',
                'summary' => 'Document management and monitoring for government efficiency.',
                'role' => 'Full-stack developer',
                'period' => '2023',
                'live_url' => 'https://dts.tesda-clgeo.com',
                'cover' => 'https://images.unsplash.com/photo-1568992687947-868a62a9f521?q=80&w=1200&auto=format&fit=crop',
                'tags' => ['Laravel', 'Filament', 'Vue.js', 'MySQL'],
                'highlights' => ['Routing history', 'Status approvals', 'Department hand-offs'],
                'body' => [
                    'Overview' => 'Tracks the lifecycle of official memos: routing history, status approvals and hand-offs between departments.',
                ],
                'featured' => false,
            ],
            [
                'title' => 'Legislative Information System',
                'summary' => 'Tracks legislative activity and budget monitoring for policy planning.',
                'role' => 'Full-stack developer',
                'period' => '2024',
                'live_url' => 'https://staging-lis.tesda.gov.ph',
                'cover' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?q=80&w=1200&auto=format&fit=crop',
                'tags' => ['Laravel', 'Vue.js', 'Inertia.js', 'TypeScript', 'MySQL'],
                'highlights' => ['Bill and policy tracking', 'Committee resolutions', 'Budget allocations'],
                'body' => [
                    'Overview' => 'Tracks legislative bills, policy updates, committee resolutions and budget allocations relevant to technical education.',
                ],
                'featured' => false,
            ],
        ];

        foreach ($projects as $position => $data) {
            $project = Project::query()->updateOrCreate(['slug' => Str::slug($data['title'])], [
                'title' => $data['title'],
                'summary' => $data['summary'],
                'body' => $this->markdownSections($data['body']),
                'role' => $data['role'],
                'period' => $data['period'],
                'highlights' => $data['highlights'],
                'cover_image' => $data['cover'],
                'live_url' => $data['live_url'],
                'repo_url' => null,
                'status' => ProjectStatus::Published,
                'is_featured' => $data['featured'],
                'published_at' => now()->subMonths(count($projects) - $position),
                'position' => $position + 1,
            ]);

            $project->tags()->sync($this->tagIds($data['tags']));

            $project->images()->delete();
            foreach ($data['gallery'] ?? [] as $index => $url) {
                $project->images()->create(['path' => $url, 'alt' => "{$data['title']} screenshot", 'position' => $index + 1]);
            }
        }
    }

    private function seedExperience(): void
    {
        $roles = [
            [
                'company' => 'TESDA',
                'role' => 'IT MIS Developer (Full-stack)',
                'location' => 'Taguig City, Philippines',
                'employment_type' => EmploymentType::FullTime,
                'started_on' => '2023-07-01',
                'ended_on' => null,
                'summary' => 'Build and maintain six concurrently running national production systems with Laravel, Filament and Vue.js.',
                'highlights' => [
                    'Shipped and maintain six concurrent production systems supporting multi-agency access across scholarship, profiling and learning-management platforms.',
                    'Designed and optimised MySQL operations for scholarship and enrolment systems, improving query performance by around 40%.',
                    'Own API integrations and Git-based workflows across the whole portfolio of systems.',
                    'Use Claude Code and other AI tools to scaffold, debug and ship production features independently.',
                ],
                'technologies' => ['Laravel', 'Filament', 'Vue.js', 'MySQL', 'Tailwind CSS', 'Git'],
            ],
            [
                'company' => 'Keen Eye Software Solutions',
                'role' => 'Front-end Developer',
                'location' => 'Remote',
                'employment_type' => EmploymentType::Freelance,
                'started_on' => '2024-10-01',
                'ended_on' => '2025-03-01',
                'summary' => 'Responsive, accessible UI components with React, TypeScript, Tailwind CSS and Inertia.js.',
                'highlights' => [
                    'Built responsive, accessible UI components in React, Tailwind CSS and TypeScript.',
                    'Integrated front-end interfaces with Laravel services via Inertia.js, closing the gap between design hand-off and delivery.',
                ],
                'technologies' => ['React', 'TypeScript', 'Inertia.js', 'Tailwind CSS', 'Laravel'],
            ],
            [
                'company' => 'Point One Integrated Technologies Inc.',
                'role' => 'Web Developer',
                'location' => 'Philippines',
                'employment_type' => EmploymentType::FullTime,
                'started_on' => '2022-09-01',
                'ended_on' => '2023-03-01',
                'summary' => 'Web applications with CodeIgniter, JavaScript and jQuery.',
                'highlights' => [
                    'Developed web applications using CodeIgniter, HTML, CSS3, JavaScript and jQuery.',
                    'Managed databases and version control with Navicat and Git.',
                ],
                'technologies' => ['CodeIgniter', 'JavaScript', 'jQuery', 'MySQL', 'Git'],
            ],
        ];

        foreach ($roles as $position => $role) {
            Experience::query()->updateOrCreate(
                ['company' => $role['company'], 'role' => $role['role']],
                [...$role, 'position' => $position + 1],
            );
        }

        $education = [
            ['institution' => 'AMA Computer Learning Center (ACLC)', 'qualification' => 'Bachelor of Science', 'field_of_study' => 'Information and Communication Systems', 'started_on' => '2018-06-01', 'ended_on' => '2022-06-01', 'notes' => 'Focused on web systems development and software engineering.'],
            ['institution' => 'Asian Institute of Computer Studies (AICS)', 'qualification' => 'Senior High School', 'field_of_study' => 'Information and Communication Technology', 'started_on' => '2016-06-01', 'ended_on' => '2018-03-01', 'notes' => null],
        ];

        foreach ($education as $position => $entry) {
            Education::query()->updateOrCreate(['institution' => $entry['institution']], [...$entry, 'position' => $position + 1]);
        }

        $certificates = [
            ['name' => 'Learn ChatGPT for Government Workforce', 'issuer' => 'Government AI Workforce Training', 'issued_on' => '2025-06-01', 'credential_url' => null],
            ['name' => 'Stay Safe Online: Cybersecurity Basics', 'issuer' => 'DICT', 'issued_on' => '2025-06-01', 'credential_url' => null],
            ['name' => 'Digital Citizenship', 'issuer' => 'National ICT Training Center', 'issued_on' => '2025-06-01', 'credential_url' => null],
        ];

        foreach ($certificates as $position => $certificate) {
            Certificate::query()->updateOrCreate(['name' => $certificate['name']], [...$certificate, 'position' => $position + 1]);
        }
    }

    private function seedPosts(): void
    {
        $post = Post::query()->updateOrCreate(['slug' => 'shipping-production-systems-with-laravel-filament-and-claude-code'], [
            'title' => 'Shipping six production systems with Laravel, Filament and Claude Code',
            'excerpt' => 'How combining Laravel, Filament and an AI-assisted workflow lets one developer keep several government systems moving.',
            'body' => <<<'MD'
                ## The workflow

                Daily use of **Claude Code** has changed how I approach software work. I hand off repetitive scaffolding and fiddly debugging, and spend my own attention on architecture, data modelling and the parts of the UI people actually touch.

                ```bash
                claude "scaffold a Filament resource for profiling records with agency-scoped access"
                ```

                ## What stays human

                AI is fast at producing code, but it doesn't know your users. Reviewing every change, keeping tests close to the business rules and saying no to unnecessary features is still the job.

                ## Takeaways

                - Let the framework do the heavy lifting — Filament covers most internal tooling.
                - Treat AI output like a pull request from a new teammate: read it.
                - Keep the database honest; most performance wins live there.
                MD,
            'status' => PostStatus::Published,
            'published_at' => now()->subWeeks(2),
        ]);

        $post->tags()->sync($this->tagIds(['Laravel', 'Filament', 'AI-assisted development']));
    }

    private function seedMessages(): void
    {
        if (ContactMessage::query()->exists()) {
            return;
        }

        ContactMessage::query()->create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@example.com',
            'subject' => 'Freelance Laravel project',
            'message' => "Hi Cedric,\n\nWe're rebuilding our booking platform on Laravel and Inertia and would love to talk about a 3-month contract. Are you available for a call next week?\n\nThanks,\nMaria",
            'created_at' => now()->subHours(5),
        ]);

        ContactMessage::query()->create([
            'name' => 'Daniel Cruz',
            'email' => 'daniel@example.com',
            'subject' => null,
            'message' => 'Really enjoyed your post about shipping with Filament. Do you have any tips for structuring multi-tenant admin panels?',
            'read_at' => now()->subDay(),
            'created_at' => now()->subDays(2),
        ]);
    }

    /**
     * @param  array<string, string>  $sections
     */
    private function markdownSections(array $sections): string
    {
        return collect($sections)
            ->map(fn (string $text, string $heading) => "## {$heading}\n\n{$text}")
            ->implode("\n\n");
    }

    /**
     * @param  list<string>  $names
     * @return list<int>
     */
    private function tagIds(array $names): array
    {
        return array_map(
            fn (string $name): int => Tag::query()->firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id,
            $names,
        );
    }
}
