<?php

namespace Database\Seeders;

use App\Domains\Experience\Enums\EmploymentType;
use App\Domains\Experience\Models\Certificate;
use App\Domains\Experience\Models\Education;
use App\Domains\Experience\Models\Experience;
use App\Domains\Profile\Enums\AvailabilityStatus;
use App\Domains\Profile\Enums\SocialPlatform;
use App\Domains\Profile\Events\ProfileChanged;
use App\Domains\Profile\Models\Profile;
use App\Domains\Profile\Models\SocialLink;
use App\Domains\Projects\Enums\ProjectStatus;
use App\Domains\Projects\Models\Project;
use App\Domains\Projects\Support\FolderTree;
use App\Domains\Services\Models\Service;
use App\Domains\Tags\Enums\TagCategory;
use App\Domains\Tags\Models\Tag;
use App\Shared\Actions\StoreUploadedFileAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Real portfolio content only. Everything here comes from the résumé, except:
 * - project live URLs, tech stacks and screenshots (from the previous portfolio, confirmed by the owner);
 * - the "Developer portfolio & CMS" case study, which describes this repository and can be checked against it.
 *
 * Nothing is invented: no testimonials, rates, engagement types, process steps, blog posts, sample
 * messages or metrics. Fill those in through the admin when they are real.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedProfile();
        $this->seedSkills();
        $this->seedProjects();
        $this->seedExperience();
        $this->seedServices();
        $this->attachResume();

        ProfileChanged::dispatch();
    }

    private function seedProfile(): void
    {
        Profile::query()->updateOrCreate([], [
            'name' => 'Cedric James Leala',
            'headline' => 'Full-Stack Web Developer',
            'tagline' => 'Full-stack web developer building with Laravel, React and TypeScript.',
            'short_bio' => 'Full-Stack Web Developer with 3+ years of experience building and maintaining government web applications using Laravel, Filament, Vue.js, React, Inertia.js and TypeScript.',
            'bio' => <<<'MD'
                Full-Stack Web Developer with 3+ years of experience building and maintaining government web applications using **Laravel, Filament, Vue.js, React, Inertia.js and TypeScript**.

                Experienced in MySQL database design and optimization, API development, Git workflows, and Docker and AWS-based deployments. Delivered national-scale systems including a multi-agency profiling platform, a learning management system and a document tracking system.

                Uses AI coding tools (Claude Code, ChatGPT, Copilot) to speed up development.
                MD,
            'location' => 'Taytay, Rizal, Philippines',
            'timezone' => 'UTC+8 (Philippine time)',
            'email' => 'cedricjamesleala128@gmail.com',
            'phone' => '+63 938 854 2688',
            'availability_status' => AvailabilityStatus::Available,
            'availability' => 'Open to part-time contracts',
            'working_hours' => null,
            'response_time' => null,
            'booking_url' => null,
            'currently_learning' => null,
            'starting_rate' => null,
            'engagement_types' => null,
            'process_steps' => null,
            'blog_enabled' => false,
            'seo_title' => 'Cedric James Leala · Full-Stack Web Developer',
            'seo_description' => 'Full-stack web developer in Taytay, Rizal, Philippines with 3+ years building government web applications with Laravel, React and TypeScript.',
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

    /**
     * The résumé's "Technical skills" section, in its order.
     */
    private function seedSkills(): void
    {
        $groups = [
            TagCategory::Backend->value => ['PHP', 'Laravel', 'Filament', 'CodeIgniter', 'REST APIs'],
            TagCategory::Database->value => ['MySQL', 'PostgreSQL'],
            TagCategory::Frontend->value => ['JavaScript', 'TypeScript', 'React', 'Vue.js', 'Inertia.js', 'HTML5', 'CSS3', 'SCSS', 'Tailwind CSS', 'Bootstrap', 'jQuery'],
            TagCategory::DevopsTools->value => ['Git', 'Docker', 'AWS', 'Linux', 'Navicat Premium'],
            TagCategory::AiTools->value => ['Claude', 'Claude Code', 'ChatGPT', 'Gemini', 'GitHub Copilot', 'Antigravity'],
        ];

        $position = 0;

        foreach ($groups as $category => $names) {
            foreach ($names as $name) {
                Tag::query()->updateOrCreate(
                    ['slug' => Str::slug($name)],
                    ['name' => $name, 'category' => $category, 'position' => ++$position],
                );
            }
        }
    }

    private function seedProjects(): void
    {
        $projects = [
            [
                'title' => 'TESDA Profiling MIS',
                'summary' => 'National profiling system with multi-agency access and real-time analytics.',
                'live_url' => 'https://mis.tesda-clgeo.com',
                'tags' => ['Laravel', 'Filament', 'Vue.js', 'MySQL', 'Tailwind CSS'],
                'featured' => true,
            ],
            $this->portfolioCaseStudy(),
            [
                'title' => 'CBTVETO Landing Site',
                'summary' => 'Public LMS, TESDA directory and document verification system.',
                'live_url' => 'https://tesda-clgeo.com',
                'cover' => '/images/projects/cbtveto-desktop.jpg',
                'gallery' => ['/images/projects/cbtveto-services.jpg', '/images/projects/cbtveto-mobile.jpg'],
                'tags' => ['Laravel', 'Vue.js', 'Tailwind CSS', 'MySQL', 'REST APIs'],
                'featured' => true,
            ],
            [
                'title' => 'CBTVET Learning Management System',
                'summary' => 'Student enrollment, courses and learning materials management.',
                'live_url' => 'https://lms.cbtveto.com',
                'tags' => ['Laravel', 'Vue.js', 'Tailwind CSS', 'MySQL'],
            ],
            [
                'title' => 'National Directory for CTECs',
                'summary' => 'Trainer profiles, association management and achievement tracker.',
                'live_url' => 'https://ctec.tesda-clgeo.com',
                'tags' => ['Laravel', 'Filament', 'Vue.js', 'MySQL'],
            ],
            [
                'title' => 'Document Tracking System (DTS)',
                'summary' => 'Document management and monitoring system for CLGEO.',
                'live_url' => 'https://dts.tesda-clgeo.com',
                'tags' => ['Laravel', 'Filament', 'Vue.js', 'MySQL'],
            ],
            [
                'title' => 'Legislative Information System (LIS)',
                'summary' => 'Tracks legislative activities and budget monitoring.',
                'live_url' => 'https://staging-lis.tesda.gov.ph',
                'tags' => ['Laravel', 'Vue.js', 'Inertia.js', 'TypeScript', 'MySQL'],
            ],
        ];

        foreach ($projects as $position => $data) {
            $project = Project::query()->updateOrCreate(['slug' => Str::slug($data['title'])], [
                'title' => $data['title'],
                'summary' => $data['summary'],
                'problem' => $data['problem'] ?? null,
                'role' => $data['role'] ?? 'Full-stack developer',
                'role_details' => $data['role_details'] ?? null,
                'architecture' => $data['architecture'] ?? null,
                'architecture_tree' => FolderTree::parse($data['tree'] ?? null) ?: null,
                'decisions' => $data['decisions'] ?? null,
                'challenges' => $data['challenges'] ?? null,
                'results' => $data['results'] ?? null,
                'metrics' => $data['metrics'] ?? null,
                'cover_image' => $data['cover'] ?? null,
                'live_url' => $data['live_url'] ?? null,
                'repo_url' => $data['repo_url'] ?? null,
                'is_confidential' => false,
                'started_on' => null,
                'ended_on' => null,
                'status' => ProjectStatus::Published,
                'is_featured' => $data['featured'] ?? false,
                'published_at' => now(),
                'position' => $position + 1,
            ]);

            $project->tags()->sync($this->tagIds($data['tags']));

            $project->images()->delete();
            foreach ($data['gallery'] ?? [] as $index => $url) {
                $project->images()->create(['path' => $url, 'alt' => "{$data['title']} screenshot", 'position' => $index + 1]);
            }
        }
    }

    /**
     * This site. Every statement below can be checked in this repository.
     *
     * @return array<string, mixed>
     */
    private function portfolioCaseStudy(): array
    {
        return [
            'title' => 'Developer Portfolio & CMS',
            'summary' => 'This site: a Laravel, Inertia and React portfolio with its own admin for every page.',
            'role_details' => <<<'MD'
                - Designed and built the whole application: data model, admin, public site and deployment setup
                - Used Claude Code as an AI pair programmer, with every change going through the same lint, type and test checks
                MD,
            'problem' => 'A portfolio whose content (projects, experience, skills, services and posts) can be updated from an admin panel, without code changes or redeploys.',
            'architecture' => <<<'MD'
                A **Laravel** back end organised by business domain instead of by framework layer. Each module in `app/Domains` owns its models, actions, data transfer objects and policies. Controllers stay thin: validate a form request, call one action, return an Inertia response.

                The front end is **React with TypeScript** over **Inertia.js**, organised by feature, with typed route helpers generated from the Laravel routes.

                Everything runs in **Docker**: PHP-FPM, Nginx, MySQL, Redis, a queue worker and a scheduler.
                MD,
            'tree' => <<<'TREE'
                app/
                  Domain/  # one folder per module
                    Projects/
                      Actions
                      DataTransferObjects
                      Models
                      Policies
                      QueryBuilders
                    Blog/
                    Experience/
                    Contact/
                  Http/
                    Controllers  # thin: request → action → response
                    Requests  # validation
                    Resources  # API shape for Inertia
                resources/
                  js/
                    features/  # UI per module
                    pages/  # Inertia pages
                    components/
                tests/
                  Feature
                  Unit
                TREE,
            'decisions' => <<<'MD'
                **Actions and DTOs for every write.** Each change (create a project, reorder tags, submit a contact form) is one action class that takes a typed data object, so the same logic is used by controllers and tests.

                **Markdown rendered on the server.** Case studies, posts and the bio are written in Markdown and converted to HTML in PHP, with raw HTML stripped, so the front end never renders untrusted markup.

                **Privacy by default on the contact form.** The sender's IP is stored only as an HMAC, spam is caught with a honeypot, and notification emails are sent from the queue so the form responds instantly.
                MD,
            'results' => <<<'MD'
                - Every page's content is editable in the admin
                - CI runs Pint, Larastan (level 7), ESLint, Prettier, TypeScript and the Pest suite on every push, then builds the production image
                MD,
            'tags' => ['Laravel', 'React', 'TypeScript', 'Inertia.js', 'Tailwind CSS', 'MySQL', 'Docker', 'Pest', 'Claude Code'],
            'featured' => true,
        ];
    }

    private function seedExperience(): void
    {
        $roles = [
            [
                'company' => 'TESDA',
                'role' => 'IT MIS Developer (Full Stack)',
                'location' => 'Taguig, Philippines',
                'employment_type' => null,
                'started_on' => '2023-07-01',
                'ended_on' => '2026-06-01',
                'highlights' => [
                    'Built and maintained web applications using Laravel, Filament and Vue.js for national government programs.',
                    'Developed and managed APIs and MySQL databases, and maintained Git workflows for team development.',
                    'Designed and optimized database operations for scholarship management systems.',
                    'Delivered systems including a national profiling MIS, a learning management system and a document tracking system.',
                ],
                'technologies' => ['Laravel', 'Filament', 'Vue.js', 'MySQL', 'Git'],
            ],
            [
                'company' => 'Keen Eye Software Solutions',
                'role' => 'Front-End Developer',
                'location' => null,
                'employment_type' => EmploymentType::Freelance,
                'started_on' => '2024-10-01',
                'ended_on' => '2025-03-01',
                'highlights' => [
                    'Designed responsive user interfaces using React, Tailwind CSS, TypeScript and Inertia.js.',
                    'Integrated the front end with Laravel back-end services.',
                ],
                'technologies' => ['React', 'Tailwind CSS', 'TypeScript', 'Inertia.js', 'Laravel'],
            ],
            [
                'company' => 'Point One Integrated Technologies Inc.',
                'role' => 'Web Developer',
                'location' => null,
                'employment_type' => null,
                'started_on' => '2022-09-01',
                'ended_on' => '2023-03-01',
                'highlights' => [
                    'Developed web applications using CodeIgniter, HTML5, CSS3, JavaScript and jQuery.',
                    'Managed databases with Navicat Premium and handled version control with Git.',
                ],
                'technologies' => ['CodeIgniter', 'HTML5', 'CSS3', 'JavaScript', 'jQuery', 'Navicat Premium', 'Git'],
            ],
        ];

        foreach ($roles as $position => $role) {
            Experience::query()->updateOrCreate(
                ['company' => $role['company'], 'role' => $role['role']],
                [...$role, 'summary' => null, 'is_remote' => false, 'is_confidential' => false, 'position' => $position + 1],
            );
        }

        // The résumé gives years only; they are stored as January and displayed as years.
        $education = [
            ['institution' => 'AMA Computer Learning Center (ACLC)', 'qualification' => 'Bachelor of Science in Information and Communication Systems', 'started_on' => '2018-01-01', 'ended_on' => '2022-01-01'],
            ['institution' => 'Asian Institute of Computer Studies (AICS)', 'qualification' => 'Information and Communication Technologies', 'started_on' => '2016-01-01', 'ended_on' => '2018-01-01'],
        ];

        foreach ($education as $position => $entry) {
            Education::query()->updateOrCreate(
                ['institution' => $entry['institution']],
                [...$entry, 'field_of_study' => null, 'notes' => null, 'position' => $position + 1],
            );
        }

        $certificates = [
            'Learn ChatGPT for Government Workforce',
            'Stay Safe Online: Cybersecurity Basics',
            'Digital Citizenship',
        ];

        foreach ($certificates as $position => $name) {
            Certificate::query()->updateOrCreate(
                ['name' => $name],
                ['issuer' => null, 'issued_on' => '2025-06-01', 'credential_url' => null, 'position' => $position + 1],
            );
        }
    }

    /**
     * What the résumé shows you have done, phrased as services. Each description restates résumé lines only.
     */
    private function seedServices(): void
    {
        $services = [
            [
                'title' => 'Laravel web application development',
                'icon' => 'server',
                'description' => 'Building and maintaining web applications with Laravel, Filament and Vue.js, the stack behind national-scale government systems such as a multi-agency profiling platform, a learning management system and a document tracking system.',
            ],
            [
                'title' => 'Front-end development',
                'icon' => 'layout-dashboard',
                'description' => 'Responsive user interfaces with React, TypeScript, Tailwind CSS and Inertia.js, integrated with Laravel back-end services.',
            ],
            [
                'title' => 'APIs & MySQL databases',
                'icon' => 'database',
                'description' => 'API development, and MySQL database design and optimization, including database operations for scholarship management systems.',
            ],
            [
                'title' => 'Deployment & version control',
                'icon' => 'container',
                'description' => 'Docker and AWS-based deployments, and Git workflows for team development.',
            ],
        ];

        foreach ($services as $position => $service) {
            Service::query()->updateOrCreate(['title' => $service['title']], [...$service, 'position' => $position + 1]);
        }
    }

    /**
     * The owner's résumé PDF, if it has been placed at storage/app/seed/resume.pdf. That folder is
     * gitignored, so the file (which has a phone number and address) never ends up in the repository.
     */
    private function attachResume(): void
    {
        $source = storage_path('app/seed/resume.pdf');

        if (! is_file($source)) {
            return;
        }

        $disk = Storage::disk(StoreUploadedFileAction::disk());
        $path = 'resume/cedric-james-leala-resume.pdf';
        $disk->put($path, (string) file_get_contents($source));

        Profile::query()->sole()->update(['resume_path' => $path, 'resume_updated_at' => now()]);
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
