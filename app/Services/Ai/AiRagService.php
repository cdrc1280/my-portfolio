<?php

namespace App\Services\Ai;

use App\Models\AiKnowledgeBase;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiRagService
{
    protected ?string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
        $this->model = env('OPENAI_MODEL', 'gpt-4o-mini');
    }

    /**
     * Generate an AI answer using Retrieval-Augmented Generation (RAG).
     */
    public function ask(string $query, array $conversationHistory = []): array
    {
        $contextChunks = $this->retrieveContext($query);
        $suggestedActions = $this->extractActions($query, $contextChunks);

        if ($this->apiKey && strlen($this->apiKey) > 10) {
            try {
                return $this->queryOpenAI($query, $contextChunks, $conversationHistory, $suggestedActions);
            } catch (\Throwable $e) {
                Log::warning('OpenAI API request failed, switching to local RAG fallback: ' . $e->getMessage());
            }
        }

        // Fallback intelligent semantic engine
        return $this->localFallbackResponse($query, $contextChunks, $suggestedActions);
    }

    /**
     * Retrieve relevant knowledge base chunks and portfolio models.
     */
    protected function retrieveContext(string $query): array
    {
        $queryLower = strtolower($query);
        $chunks = [];

        // 1. Search AiKnowledgeBase table
        $kbItems = AiKnowledgeBase::all();
        foreach ($kbItems as $item) {
            $score = 0;
            if (Str::contains($queryLower, strtolower($item->topic))) $score += 3;
            if (Str::contains($queryLower, strtolower($item->title))) $score += 2;
            if (Str::contains($queryLower, strtolower($item->category))) $score += 1;

            foreach (explode(' ', $queryLower) as $word) {
                if (strlen($word) > 3 && Str::contains(strtolower($item->content), $word)) {
                    $score += 1;
                }
            }

            if ($score > 0) {
                $chunks[] = [
                    'source' => "KnowledgeBase: {$item->title}",
                    'content' => $item->content,
                    'score' => $score,
                ];
            }
        }

        // 2. Search Projects
        if (Str::contains($queryLower, ['project', 'laravel', 'vue', '3d', 'rag', 'nexus', 'aetherium', 'stratos', 'code', 'portfolio'])) {
            $projects = Project::with('category')->get();
            foreach ($projects as $project) {
                $chunks[] = [
                    'source' => "Project: {$project->title}",
                    'content' => "Project Title: {$project->title}. Summary: {$project->summary}. Tech Stack: " . implode(', ', $project->tech_stack ?? []) . ". Architecture: {$project->architecture}. Demo: {$project->demo_url}",
                    'score' => 2,
                ];
            }
        }

        // 3. Search Skills
        if (Str::contains($queryLower, ['skill', 'stack', 'tech', 'php', 'laravel', 'three.js', 'vue', 'docker', 'mysql', 'experience'])) {
            $skills = Skill::all();
            $skillNames = $skills->pluck('name')->implode(', ');
            $chunks[] = [
                'source' => "Skills Directory",
                'content' => "Cedric's top technical skills and mastery: {$skillNames}.",
                'score' => 3,
            ];
        }

        // Sort by relevance score
        usort($chunks, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($chunks, 0, 5);
    }

    /**
     * Extract suggested UI actions based on intent.
     */
    protected function extractActions(string $query, array $context): array
    {
        $actions = [];
        $queryLower = strtolower($query);

        if (Str::contains($queryLower, ['project', 'work', 'build', 'show', 'portfolio'])) {
            $actions[] = ['label' => 'View Projects Showcase', 'action' => 'scroll_to', 'target' => '#projects'];
            $actions[] = ['label' => 'Find Laravel Projects', 'action' => 'filter_projects', 'category' => 'backend-ai'];
        }

        if (Str::contains($queryLower, ['skill', 'tech', 'stack', 'vue', 'three.js'])) {
            $actions[] = ['label' => 'Explore 3D Skill Galaxy', 'action' => 'scroll_to', 'target' => '#skills'];
        }

        if (Str::contains($queryLower, ['resume', 'cv', 'hire', 'experience'])) {
            $actions[] = ['label' => 'Download Cedric\'s Resume (PDF)', 'action' => 'download_resume'];
            $actions[] = ['label' => 'View Career Experience', 'action' => 'scroll_to', 'target' => '#experience'];
        }

        if (Str::contains($queryLower, ['contact', 'email', 'hire', 'reach', 'consult'])) {
            $actions[] = ['label' => 'Contact Cedric directly', 'action' => 'scroll_to', 'target' => '#contact'];
        }

        if (empty($actions)) {
            $actions = [
                ['label' => 'Show Backend Projects', 'action' => 'filter_projects', 'category' => 'backend-ai'],
                ['label' => 'Download Resume', 'action' => 'download_resume'],
                ['label' => 'Book Consultation', 'action' => 'scroll_to', 'target' => '#contact'],
            ];
        }

        return $actions;
    }

    /**
     * Query OpenAI Chat Completions API.
     */
    protected function queryOpenAI(string $query, array $contextChunks, array $history, array $suggestedActions): array
    {
        $contextText = implode("\n\n", array_map(fn($c) => "[{$c['source']}]\n{$c['content']}", $contextChunks));

        $systemPrompt = "You are AetherAI, the intelligent assistant embedded inside Cedric James Leala's world-class portfolio website. "
            . "Cedric is a Full Stack Developer and AI-Assisted Development Specialist (Claude Code) with 3 years of professional experience shipping 6 production systems for TESDA. "
            . "Answer the user's inquiry accurately, professionally, and concisely using the provided context below. "
            . "Format your output with beautiful Markdown (bold text, code blocks, bullet points).\n\n"
            . "Context:\n" . $contextText;

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? '',
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $query];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? 'I am ready to assist you with Cedric\'s portfolio!';
            return [
                'reply' => $reply,
                'suggested_actions' => $suggestedActions,
            ];
        }

        throw new \Exception("OpenAI API returned HTTP " . $response->status());
    }

    /**
     * Intelligent local fallback RAG response generator.
     */
    protected function localFallbackResponse(string $query, array $contextChunks, array $suggestedActions): array
    {
        $queryLower = strtolower($query);

        if (Str::contains($queryLower, ['skill', 'strongest', 'tech', 'stack'])) {
            $reply = "### Cedric's Core Technical Stack\n\n"
                . "Cedric James Leala has **3 years of professional experience** shipping and maintaining six concurrently running production systems for TESDA:\n\n"
                . "- **AI-Assisted Development**: Daily hands-on use of Claude Code CLI for scaffolding & debugging, plus ChatGPT & Gemini for code review.\n"
                . "- **Backend & Database**: Laravel 12 (PHP 8.4), Filament v4, MySQL Database Design & Query Optimization, REST APIs.\n"
                . "- **Frontend Engineering**: Vue 3, React.js, TypeScript, Inertia.js, Tailwind CSS, Bootstrap, HTML/CSS/SCSS.\n\n"
                . "Feel free to check out the **3D Skill Galaxy** section to see interactive planet orbits of his tech stack!";
        } elseif (Str::contains($queryLower, ['project', 'work', 'laravel', 'built'])) {
            $reply = "### Highlighted TESDA Production Systems\n\n"
                . "Cedric has shipped and maintained six concurrently running national systems:\n\n"
                . "1. **TESDA Profiling MIS** (mis.tesda-clgeo.com): National profiling system with multi-agency access.\n"
                . "2. **CBTVETO Landing Site** (tesda-clgeo.com): Public LMS, TESDA directory, and document verification.\n"
                . "3. **National Directory for CTECs** (ctec.tesda-clgeo.com): Trainers' profiles & achievement tracker.\n"
                . "4. **CBTVET LMS** (lms.cbtveto.com): Student enrollment and learning materials portal.\n"
                . "5. **Document Tracking System DTS** (dts.tesda-clgeo.com): CLGEO document workflow monitoring.\n"
                . "6. **Legislative Info System LIS** (staging-lis.tesda.gov.ph): Tracks legislative bills and budget monitoring.\n\n"
                . "Click below to jump directly to the **Projects Showcase** section!";
        } elseif (Str::contains($queryLower, ['resume', 'cv', 'experience', 'career'])) {
            $reply = "### Cedric James Leala's Career & Experience Summary\n\n"
                . "- **IT MIS Developer (Full Stack)** @ TESDA, Taguig (Jul 2023 – Jun 2026): Shipped & maintained 6 concurrent production systems.\n"
                . "- **Front-End Developer (Freelance)** @ Keen Eye Software Solutions (Oct 2024 – Mar 2025): React.js, TypeScript & Inertia.js.\n"
                . "- **Web Developer** @ Point One Integrated Technologies Inc. (Sep 2022 – Mar 2023): CodeIgniter, JS & MySQL.\n"
                . "- **Education**: BS in Information & Communication Systems from AMA Computer Learning Center (ACLC, 2018–2022).\n\n"
                . "You can download Cedric's complete resume using the button below!";
        } elseif (Str::contains($queryLower, ['contact', 'email', 'hire', 'consult'])) {
            $reply = "### Get in Touch with Cedric James Leala\n\n"
                . "Cedric is available for **Full Stack Web & Laravel Filament Development** and AI-assisted development opportunities.\n\n"
                . "- **Email**: `cedricjamesleala128@gmail.com`\n"
                . "- **Phone**: `0938-854-2688`\n"
                . "- **Location**: `Sitio Gulod Muzon, Taytay, Rizal 1920`\n"
                . "- **Status**: 🟢 Available for full-stack engagements\n\n"
                . "Use the contact form below or reach out directly via email!";
        } else {
            $contextContent = count($contextChunks) > 0 ? $contextChunks[0]['content'] : "Cedric James Leala is a Full Stack Developer specializing in AI-assisted development.";
            $reply = "### Greetings! I am AetherAI\n\n"
                . "I am Cedric James Leala's portfolio AI assistant powered by Retrieval-Augmented Generation (RAG).\n\n"
                . "{$contextContent}\n\n"
                . "How can I assist you today? You can ask about Cedric's **TESDA production systems**, **Laravel & Filament v4 experience**, **Vue.js & React skills**, or **download his resume**.";
        }

        return [
            'reply' => $reply,
            'suggested_actions' => $suggestedActions,
        ];
    }
}
