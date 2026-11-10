<?php

namespace Database\Seeders;

use App\Models\AiKnowledgeBase;
use App\Models\Blog;
use App\Models\BlogCategory;
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
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Site Settings (Cedric James Leala's Real Info & AI-Assisted Summary)
        $settings = [
            ['key' => 'hero_name', 'value' => 'Cedric James Leala', 'group' => 'hero'],
            ['key' => 'hero_title', 'value' => 'Full Stack Developer | AI-Assisted Development (Claude Code)', 'group' => 'hero'],
            ['key' => 'hero_subtitle', 'value' => 'Full Stack Developer with three years of experience shipping and maintaining six concurrently running production systems for TESDA. Daily practitioner of AI-assisted workflows using Claude Code, ChatGPT & Gemini to build production-ready software fast.', 'group' => 'hero'],
            ['key' => 'professional_summary', 'value' => 'Full Stack Developer with three years of experience shipping and maintaining six concurrently running production systems for TESDA, owning each end to end — schema, API, and UI. Core stack is Laravel, Filament, Vue.js, and MySQL, with additional front-end experience in React, TypeScript, and Inertia.js from concurrent freelance work. Uses Claude Code and other AI coding assistants as part of a daily build-test-ship workflow to move fast without sacrificing maintainability, and is comfortable taking a scoped problem from requirements to working, production-ready software independently.', 'group' => 'hero'],
            ['key' => 'profile_image', 'value' => '/images/cedric-profile.jpg', 'group' => 'hero'],
            ['key' => 'availability_status', 'value' => 'Available for Full Stack & AI-Assisted Laravel/Filament Development', 'group' => 'hero'],
            ['key' => 'location', 'value' => 'Sitio Gulod Muzon, Taytay, Rizal 1920', 'group' => 'contact'],
            ['key' => 'phone', 'value' => '0938-854-2688', 'group' => 'contact'],
            ['key' => 'email', 'value' => 'cedricjamesleala128@gmail.com', 'group' => 'contact'],
            ['key' => 'github', 'value' => 'https://github.com/cdrc1280', 'group' => 'contact'],
            ['key' => 'years_of_experience', 'value' => '3', 'group' => 'stats'],
            ['key' => 'production_systems', 'value' => '6', 'group' => 'stats'],
            ['key' => 'happy_clients', 'value' => '6', 'group' => 'stats'],
            ['key' => 'system_uptime', 'value' => '100%', 'group' => 'stats'],
            ['key' => 'theme_mode', 'value' => 'aurora', 'group' => 'theme'],
            ['key' => 'ambient_audio_enabled', 'value' => 'true', 'group' => 'theme'],
        ];

        foreach ($settings as $s) {
            SiteSetting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 1.5. Profile Media Container
        ProfileMedia::create([
            'title' => 'Cedric James Leala — Mountain Summit Profile Photo',
            'caption' => 'Full Stack Developer & AI-Assisted Engineer',
            'image' => '/images/cedric-profile.jpg',
            'is_active' => true,
            'order' => 1,
        ]);

        // 2. Skill Categories & Skills
        $scAi = SkillCategory::create(['name' => 'AI-Assisted Development', 'slug' => 'ai-assisted-dev', 'icon' => 'bot', 'order' => 1]);
        $scProficient = SkillCategory::create(['name' => 'Proficient Mastery', 'slug' => 'proficient-skills', 'icon' => 'code', 'order' => 2]);
        $scFamiliar = SkillCategory::create(['name' => 'Familiar Technologies', 'slug' => 'familiar-skills', 'icon' => 'cpu', 'order' => 3]);

        $skills = [
            // AI-Assisted Development
            ['skill_category_id' => $scAi->id, 'name' => 'Claude Code CLI & Agentic Workflows', 'proficiency' => 99, 'orbit_radius' => 2.5, 'color' => '#a855f7', 'years_of_experience' => 2, 'projects_count' => 12],
            ['skill_category_id' => $scAi->id, 'name' => 'ChatGPT & Gemini (Code Review & Docs)', 'proficiency' => 95, 'orbit_radius' => 3.2, 'color' => '#10b981', 'years_of_experience' => 3, 'projects_count' => 15],

            // Proficient
            ['skill_category_id' => $scProficient->id, 'name' => 'Laravel', 'proficiency' => 96, 'orbit_radius' => 3.8, 'color' => '#ff2d20', 'years_of_experience' => 3, 'projects_count' => 12],
            ['skill_category_id' => $scProficient->id, 'name' => 'Laravel Filament', 'proficiency' => 96, 'orbit_radius' => 4.4, 'color' => '#f59e0b', 'years_of_experience' => 3, 'projects_count' => 8],
            ['skill_category_id' => $scProficient->id, 'name' => 'Vue.js', 'proficiency' => 92, 'orbit_radius' => 5.0, 'color' => '#42b883', 'years_of_experience' => 3, 'projects_count' => 10],
            ['skill_category_id' => $scProficient->id, 'name' => 'Tailwind CSS', 'proficiency' => 95, 'orbit_radius' => 5.6, 'color' => '#38bdf8', 'years_of_experience' => 3, 'projects_count' => 12],
            ['skill_category_id' => $scProficient->id, 'name' => 'JavaScript', 'proficiency' => 90, 'orbit_radius' => 6.2, 'color' => '#f7df1e', 'years_of_experience' => 4, 'projects_count' => 15],
            ['skill_category_id' => $scProficient->id, 'name' => 'HTML, CSS & SCSS', 'proficiency' => 98, 'orbit_radius' => 6.8, 'color' => '#e34f26', 'years_of_experience' => 5, 'projects_count' => 20],
            ['skill_category_id' => $scProficient->id, 'name' => 'MySQL', 'proficiency' => 94, 'orbit_radius' => 7.2, 'color' => '#00758f', 'years_of_experience' => 3, 'projects_count' => 12],
            ['skill_category_id' => $scProficient->id, 'name' => 'Git & GitHub Workflows', 'proficiency' => 94, 'orbit_radius' => 7.8, 'color' => '#f05032', 'years_of_experience' => 4, 'projects_count' => 15],
            ['skill_category_id' => $scProficient->id, 'name' => 'Bootstrap', 'proficiency' => 90, 'orbit_radius' => 8.2, 'color' => '#7952b3', 'years_of_experience' => 4, 'projects_count' => 8],

            // Familiar
            ['skill_category_id' => $scFamiliar->id, 'name' => 'Inertia.js', 'proficiency' => 86, 'orbit_radius' => 4.0, 'color' => '#9553e9', 'years_of_experience' => 2, 'projects_count' => 5],
            ['skill_category_id' => $scFamiliar->id, 'name' => 'TypeScript', 'proficiency' => 84, 'orbit_radius' => 4.6, 'color' => '#3178c6', 'years_of_experience' => 2, 'projects_count' => 4],
            ['skill_category_id' => $scFamiliar->id, 'name' => 'React.js', 'proficiency' => 82, 'orbit_radius' => 5.2, 'color' => '#61dafb', 'years_of_experience' => 2, 'projects_count' => 3],
            ['skill_category_id' => $scFamiliar->id, 'name' => 'REST API Integration', 'proficiency' => 92, 'orbit_radius' => 5.8, 'color' => '#00f0ff', 'years_of_experience' => 3, 'projects_count' => 10],
            ['skill_category_id' => $scFamiliar->id, 'name' => 'Livewire', 'proficiency' => 80, 'orbit_radius' => 6.4, 'color' => '#fb70a9', 'years_of_experience' => 2, 'projects_count' => 3],
            ['skill_category_id' => $scFamiliar->id, 'name' => 'Alpine.js', 'proficiency' => 85, 'orbit_radius' => 7.0, 'color' => '#77c1d2', 'years_of_experience' => 2, 'projects_count' => 4],
        ];

        foreach ($skills as $i => $sk) {
            Skill::create(array_merge($sk, ['order' => $i + 1]));
        }

        // 3. Project Categories
        $catGov = ProjectCategory::create(['name' => 'TESDA Production Systems', 'slug' => 'tesda-systems', 'color' => '#3b82f6', 'order' => 1]);
        $catLms = ProjectCategory::create(['name' => 'EdTech & Learning Management', 'slug' => 'edtech-lms', 'color' => '#8b5cf6', 'order' => 2]);
        $catTools = ProjectCategory::create(['name' => 'Document & Legislative Tracking', 'slug' => 'tracking-tools', 'color' => '#10b981', 'order' => 3]);

        // 4. Projects (With High-Res Image Containers for Showcase)
        Project::create([
            'project_category_id' => $catGov->id,
            'title' => 'TESDA Profiling MIS',
            'slug' => 'tesda-profiling-mis',
            'summary' => 'National profiling system with multi-agency access, scholarship management, and real-time analytics.',
            'description' => 'Built and maintained using Laravel, Filament, and Vue.js. Features automated database operations for scholarship systems, multi-agency role permission gating, and high-performance MySQL query optimization.',
            'hero_image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1200&auto=format&fit=crop',
            'gallery' => [
                'https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1504868584819-f8e8b4b6d7e3?q=80&w=1200&auto=format&fit=crop',
            ],
            'demo_url' => 'https://mis.tesda-clgeo.com',
            'github_url' => 'https://github.com/cdrc1280',
            'tech_stack' => ['Laravel', 'Filament v4', 'Vue.js', 'MySQL', 'Tailwind CSS', 'Claude Code AI'],
            'timeline' => '2023 - 2026',
            'features' => [
                'Multi-agency role-based access control (RBAC)',
                'Real-time scholarship profiling & analytics',
                'Optimized MySQL indexing for fast query execution',
                'Filament administrative dashboards',
            ],
            'architecture' => 'Laravel MVC backend serving REST APIs to Vue.js frontend with Filament Admin management.',
            'challenges' => 'Handling multi-agency data aggregation and ensuring sub-second profiling queries across thousands of records.',
            'solutions' => 'Designed efficient database indexing and chunked query execution pipelines.',
            'lessons_learned' => 'Leveraging Claude Code AI and Laravel Filament speeds up government dashboard development by 5x.',
            'is_featured' => true,
            'stars_count' => 128,
            'order' => 1,
        ]);

        Project::create([
            'project_category_id' => $catLms->id,
            'title' => 'CBTVETO Landing Site & Directory',
            'slug' => 'cbtveto-landing-site',
            'summary' => 'Public LMS portal, TESDA directory, and automated document verification system.',
            'description' => 'Public-facing directory and verification platform serving TESDA institutions, trainers, and students with real-time certificate validation.',
            'hero_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop',
            'gallery' => [
                'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1200&auto=format&fit=crop',
                'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=1200&auto=format&fit=crop',
            ],
            'demo_url' => 'https://tesda-clgeo.com',
            'github_url' => 'https://github.com/cdrc1280',
            'tech_stack' => ['Laravel', 'Vue.js', 'Tailwind CSS', 'MySQL', 'API Integration'],
            'timeline' => '2024',
            'features' => [
                'Public TESDA institutional directory',
                'Document & certificate verification engine',
                'Responsive UI with Vue.js & Tailwind CSS',
            ],
            'is_featured' => true,
            'stars_count' => 94,
            'order' => 2,
        ]);

        Project::create([
            'project_category_id' => $catGov->id,
            'title' => 'National Directory for CTECs',
            'slug' => 'national-directory-ctecs',
            'summary' => 'Trainers\' profiles, association management, and national achievement tracker.',
            'description' => 'Centralized directory managing community technical education center (CTEC) trainers, association memberships, and achievement milestones across regions.',
            'hero_image' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=1200&auto=format&fit=crop',
            'demo_url' => 'https://ctec.tesda-clgeo.com',
            'github_url' => 'https://github.com/cdrc1280',
            'tech_stack' => ['Laravel', 'Filament', 'Vue.js', 'MySQL'],
            'timeline' => '2024',
            'is_featured' => true,
            'stars_count' => 86,
            'order' => 3,
        ]);

        Project::create([
            'project_category_id' => $catLms->id,
            'title' => 'CBTVET Learning Management System',
            'slug' => 'cbtvet-lms',
            'summary' => 'Student enrollment, courses, and learning materials management portal.',
            'description' => 'Full-featured Learning Management System facilitating student registration, course uploads, assignment tracking, and grade recording.',
            'hero_image' => 'https://images.unsplash.com/photo-1501504905252-473c47e087f8?q=80&w=1200&auto=format&fit=crop',
            'demo_url' => 'https://lms.cbtveto.com',
            'github_url' => 'https://github.com/cdrc1280',
            'tech_stack' => ['Laravel', 'Vue.js', 'Tailwind CSS', 'MySQL'],
            'timeline' => '2023 - 2024',
            'is_featured' => true,
            'stars_count' => 110,
            'order' => 4,
        ]);

        Project::create([
            'project_category_id' => $catTools->id,
            'title' => 'Document Tracking System (DTS)',
            'slug' => 'dts-document-tracking-system',
            'summary' => 'CLGEO document management and monitoring system for government efficiency.',
            'description' => 'Automated document lifecycle tracking system keeping record of official memos, routing histories, status approvals, and department handoffs.',
            'hero_image' => 'https://images.unsplash.com/photo-1568992687947-868a62a9f521?q=80&w=1200&auto=format&fit=crop',
            'demo_url' => 'https://dts.tesda-clgeo.com',
            'github_url' => 'https://github.com/cdrc1280',
            'tech_stack' => ['Laravel', 'Filament', 'Vue.js', 'MySQL'],
            'timeline' => '2023',
            'is_featured' => false,
            'stars_count' => 75,
            'order' => 5,
        ]);

        Project::create([
            'project_category_id' => $catTools->id,
            'title' => 'Legislative Information System (LIS)',
            'slug' => 'lis-legislative-information-system',
            'summary' => 'Tracks legislative activities and budget monitoring for TESDA policy planning.',
            'description' => 'Tracks legislative bills, policy updates, congressional committee resolutions, and budget allocations concerning technical education.',
            'hero_image' => 'https://images.unsplash.com/photo-1450133064473-71024230f91b?q=80&w=1200&auto=format&fit=crop',
            'demo_url' => 'https://staging-lis.tesda.gov.ph',
            'github_url' => 'https://github.com/cdrc1280',
            'tech_stack' => ['Laravel', 'Vue.js', 'Inertia.js', 'TypeScript', 'MySQL'],
            'timeline' => '2024',
            'is_featured' => false,
            'stars_count' => 88,
            'order' => 6,
        ]);

        // 5. Professional Experiences (Real Experience from CV)
        Experience::create([
            'company' => 'TESDA',
            'position' => 'IT MIS Developer (Full Stack)',
            'location' => 'Taguig City, Philippines',
            'start_date' => '2023-07-01',
            'end_date' => '2026-06-30',
            'is_current' => true,
            'summary' => 'Built and maintained six concurrently running national production systems using Laravel, Filament, and Vue.js.',
            'responsibilities' => [
                'Built and maintained multiple national-scale web applications using Laravel, Filament, and Vue.js, supporting multi-agency access across scholarship, profiling, and learning management systems.',
                'Designed and optimized MySQL database operations for scholarship and enrollment systems, improving data integrity and query performance in a live government environment.',
                'Managed API integrations and Git-based version control workflows across a portfolio of six concurrently maintained production systems.',
                'Leveraged Claude Code and AI tools to rapidly scaffold, debug, and ship production-ready features independently.',
            ],
            'achievements' => [
                'Successfully shipped 6 concurrent production systems for TESDA.',
                'Improved database query speed by 40% in government scholarship profiling.',
            ],
            'skills_used' => ['Laravel', 'Laravel Filament', 'Vue.js', 'MySQL', 'Git', 'Tailwind CSS', 'Claude Code AI'],
            'order' => 1,
        ]);

        Experience::create([
            'company' => 'Keen Eye Software Solutions',
            'position' => 'Front-End Developer (Freelance)',
            'location' => 'Remote',
            'start_date' => '2024-10-01',
            'end_date' => '2025-03-31',
            'is_current' => false,
            'summary' => 'Designed responsive, accessible UI components using React.js, Tailwind CSS, TypeScript, and Inertia.js.',
            'responsibilities' => [
                'Designed responsive, accessible UI components using React.js, Tailwind CSS, and TypeScript.',
                'Integrated front-end interfaces with Laravel back-end services using Inertia.js, closing the gap between design handoff and functional delivery.',
            ],
            'skills_used' => ['React.js', 'TypeScript', 'Inertia.js', 'Tailwind CSS', 'Laravel'],
            'order' => 2,
        ]);

        Experience::create([
            'company' => 'Point One Integrated Technologies Inc.',
            'position' => 'Web Developer',
            'location' => 'Philippines',
            'start_date' => '2022-09-01',
            'end_date' => '2023-03-31',
            'is_current' => false,
            'summary' => 'Developed web applications using CodeIgniter, HTML, CSS3, JavaScript, and jQuery.',
            'responsibilities' => [
                'Developed web applications using CodeIgniter, HTML, CSS3, JavaScript, and jQuery.',
                'Managed databases and version control using Navicat Premium and Git.',
            ],
            'skills_used' => ['CodeIgniter', 'HTML5', 'CSS3', 'JavaScript', 'jQuery', 'MySQL', 'Navicat', 'Git'],
            'order' => 3,
        ]);

        // 6. Education
        Education::create([
            'institution' => 'AMA Computer Learning Center (ACLC)',
            'degree' => 'Bachelor of Science',
            'field_of_study' => 'Information and Communication Systems',
            'start_date' => '2018-06-01',
            'end_date' => '2022-06-30',
            'grade' => 'Graduated 2022',
            'achievements' => ['Focused on Web Systems Development & Software Engineering'],
            'order' => 1,
        ]);

        Education::create([
            'institution' => 'Asian Institute of Computer Studies (AICS)',
            'degree' => 'Senior High School Track',
            'field_of_study' => 'Information and Communication Technologies (ICT)',
            'start_date' => '2016-06-01',
            'end_date' => '2018-03-31',
            'grade' => 'Graduated 2018',
            'achievements' => ['Academic Excellence in ICT & Programming'],
            'order' => 2,
        ]);

        // 7. Certificates & Seminars
        Certificate::create([
            'title' => 'Learn ChatGPT for Government Workforce',
            'issuer' => 'Government AI Workforce Training',
            'issue_date' => '2025-06-15',
            'category' => 'AI Development',
            'credential_url' => 'https://tesda.gov.ph',
            'image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=800&auto=format&fit=crop',
            'order' => 1,
        ]);

        Certificate::create([
            'title' => 'Stay Safe Online: Cybersecurity Basics',
            'issuer' => 'DICT / Cybersecurity Education',
            'issue_date' => '2025-06-10',
            'category' => 'Cybersecurity',
            'credential_url' => 'https://dict.gov.ph',
            'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?q=80&w=800&auto=format&fit=crop',
            'order' => 2,
        ]);

        Certificate::create([
            'title' => 'Digital Citizenship',
            'issuer' => 'National ICT Training Center',
            'issue_date' => '2025-06-05',
            'category' => 'Digital Governance',
            'credential_url' => 'https://dict.gov.ph',
            'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=800&auto=format&fit=crop',
            'order' => 3,
        ]);

        // 8. Services
        Service::create([
            'title' => 'Full Stack & AI-Assisted Laravel/Filament Development',
            'slug' => 'fullstack-ai-assisted-laravel-filament',
            'short_description' => 'Rapid, production-ready web application delivery using Laravel 12, Filament v4, Vue.js, and Claude Code AI.',
            'description' => 'Building enterprise web systems, government profiling portals, and custom admin dashboards with daily AI-assisted scaffolding and test-driven workflows.',
            'icon' => 'bot',
            'features' => ['Laravel 12 & PHP 8.4', 'Filament v4 Admin Resources', 'Claude Code AI Workflows', 'MySQL Schema & Query Optimization'],
            'badge' => 'Core Mastery',
            'order' => 1,
        ]);

        Service::create([
            'title' => 'Front-End React, Vue & Inertia.js Integration',
            'slug' => 'frontend-react-vue-inertia-integration',
            'short_description' => 'Responsive, accessible UIs built with Vue.js, React.js, Tailwind CSS, TypeScript, and Inertia.js.',
            'description' => 'Bridge the gap between UI design handoffs and functional backend REST API integrations.',
            'icon' => 'code',
            'features' => ['Vue 3 Composition API', 'React.js & TypeScript', 'Inertia.js Full Stack Sync', 'Tailwind CSS v4'],
            'badge' => 'High Speed',
            'order' => 2,
        ]);

        // 9. References as Testimonials
        Testimonial::create([
            'client_name' => 'Ramil R. Bengco',
            'position' => 'Data Science Analyst',
            'company' => 'IQVIA Solutions',
            'content' => 'Cedric James Leala is a highly dependable Full Stack Developer. His mastery of Laravel, database architecture, and AI-assisted workflows makes him an outstanding engineering asset.',
            'rating' => 5,
            'is_featured' => true,
        ]);

        Testimonial::create([
            'client_name' => 'John Jeremie Jacinto',
            'position' => 'IT MIS Developer',
            'company' => 'TESDA',
            'content' => 'Cedric\'s work across six concurrently running production systems at TESDA demonstrated exceptional full-stack capabilities, clean Laravel code, and reliable API delivery.',
            'rating' => 5,
            'is_featured' => true,
        ]);

        // 10. FAQs
        Faq::create([
            'question' => 'How does Cedric use AI-Assisted Development with Claude Code?',
            'answer' => 'Cedric leverages Claude Code daily for scaffolding, rapid debugging, and iterating on production code, alongside ChatGPT and Gemini for code reviews and documentation. This AI-assisted workflow allows him to ship robust software 3x faster without sacrificing maintainability.',
            'category' => 'AI Workflows',
            'order' => 1,
        ]);

        Faq::create([
            'question' => 'Can all website content be modified in the Filament Admin Panel?',
            'answer' => 'Yes! Cedric has built a complete Laravel Filament v4 Admin Panel at /admin where everything—projects, skills, images, hero texts, settings, certificates, experiences, and AI knowledge base items—can be dynamically edited.',
            'category' => 'Admin Panel',
            'order' => 2,
        ]);

        // 11. Timeline Events
        TimelineEvent::create(['year' => '2023 - 2026', 'title' => 'IT MIS Developer (Full Stack) @ TESDA', 'description' => 'Shipped and maintained 6 concurrent production systems using Laravel, Filament, and Vue.js with AI-assisted workflows.', 'category' => 'Career', 'icon' => 'bot', 'order' => 1]);
        TimelineEvent::create(['year' => '2024 - 2025', 'title' => 'Freelance Front-End Developer @ Keen Eye', 'description' => 'React.js, Tailwind, TypeScript, and Inertia.js front-end delivery.', 'category' => 'Career', 'icon' => 'code', 'order' => 2]);
        TimelineEvent::create(['year' => '2022 - 2023', 'title' => 'Web Developer @ Point One Tech', 'description' => 'CodeIgniter, HTML/CSS/JS, and MySQL development.', 'category' => 'Career', 'icon' => 'terminal', 'order' => 3]);

        // 12. Blog & Categories
        $bcTech = BlogCategory::create(['name' => 'AI-Assisted Development', 'slug' => 'ai-assisted-development']);

        Blog::create([
            'blog_category_id' => $bcTech->id,
            'title' => 'Shipping 6 Production Systems at Scale using Laravel, Filament & Claude Code',
            'slug' => 'shipping-production-systems-laravel-filament-claude-code',
            'summary' => 'A practical guide on combining Laravel 12, Filament v4, and Claude Code AI agent workflows to ship production-ready government web apps fast.',
            'content' => "### The AI-Assisted Developer Workflow\n\nDaily hands-on use of **Claude Code** has transformed how I approach software engineering. By delegating repetitive boilerplate scaffolding and complex regex debugging to Claude Code, I focus on system architecture, database optimization, and high-impact UI components...\n\n```bash\nclaude \"scaffold Laravel Filament resource for TESDA Profiling MIS with multi-agency role gating\"\n```",
            'cover_image' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=1200&auto=format&fit=crop',
            'reading_time' => 4,
            'tags' => ['Claude Code', 'Laravel', 'Filament v4', 'AI Development', 'Vue.js'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        // 13. Social Links
        SocialLink::create(['platform' => 'GitHub', 'url' => 'https://github.com/cdrc1280', 'icon' => 'github', 'order' => 1]);
        SocialLink::create(['platform' => 'Email', 'url' => 'mailto:cedricjamesleala128@gmail.com', 'icon' => 'mail', 'order' => 2]);

        // 14. AI Knowledge Base Items
        $kbData = [
            [
                'topic' => 'Cedric James Leala Summary',
                'title' => 'Professional Summary & AI Workflows',
                'category' => 'Profile',
                'content' => 'Cedric James Leala is a Full Stack Developer specializing in AI-Assisted Development (Claude Code). He has close to 3 years experience shipping and maintaining 6 concurrent production systems for TESDA (owning schema, API, and UI). Core stack: Laravel, Filament, Vue.js, and MySQL, plus React.js, TypeScript, and Inertia.js.',
            ],
            [
                'topic' => 'Cedric Technical Skills',
                'title' => 'Technical Skills & Tools',
                'category' => 'Skills',
                'content' => 'AI-Assisted: Claude Code CLI, ChatGPT, Gemini.\nProficient: HTML, CSS, SCSS, JavaScript, Laravel, Laravel Filament, Vue.js, Tailwind CSS, Bootstrap, Git, MySQL.\nFamiliar: Inertia.js, TypeScript, React.js, REST API Integration, Livewire, Alpine.js.',
            ],
            [
                'topic' => 'Cedric TESDA Projects',
                'title' => 'Production Systems at TESDA',
                'category' => 'Projects',
                'content' => '1. TESDA Profiling MIS (mis.tesda-clgeo.com)\n2. CBTVETO Landing Site (tesda-clgeo.com)\n3. National Directory for CTECs (ctec.tesda-clgeo.com)\n4. CBTVET LMS (lms.cbtveto.com)\n5. Document Tracking System DTS (dts.tesda-clgeo.com)\n6. Legislative Info System LIS (staging-lis.tesda.gov.ph)',
            ],
        ];

        foreach ($kbData as $kb) {
            AiKnowledgeBase::create($kb);
        }
    }
}
