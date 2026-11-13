<?php

declare(strict_types=1);

namespace App\Domains\Assistant\Queries;

use App\Domains\Assistant\ValueObjects\AssistantReply;
use App\Domains\Experience\Models\Certificate;
use App\Domains\Experience\Models\Education;
use App\Domains\Experience\Models\Experience;
use App\Domains\Profile\Actions\GetProfileAction;
use App\Domains\Profile\Models\Profile;
use App\Domains\Projects\Models\Project;
use App\Domains\Services\Models\Service;
use App\Domains\Tags\Models\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * The free "Ask about me" assistant. It matches a visitor's question to a topic (skills, projects,
 * experience, availability, …) and answers only with content from this site's database. There is no
 * language model, so it can't invent anything, and it costs nothing to run. A question it can't
 * match gets an honest "I don't know" plus suggestions and a link to the contact form.
 */
final class AnswerQuestionQuery
{
    /**
     * Topic → word patterns, matched as whole words. A trailing `*` matches any word ending
     * ("certif*" → certificate, certification). Order breaks ties.
     */
    private const TOPICS = [
        'rates' => ['rate*', 'price*', 'pricing', 'cost*', 'charge*', 'budget*', 'hourly', 'fee*', 'pay', 'salary', 'how much', 'magkano'],
        'availability' => ['availab*', 'hire', 'hiring', 'free for', 'open to', 'contract*', 'part-time', 'part time', 'full-time', 'full time', 'freelanc*', 'take on', 'looking for work', 'job*'],
        'contact' => ['contact*', 'email*', 'e-mail', 'reach', 'message you', 'call*', 'book*', 'meeting*', 'talk', 'phone', 'get in touch'],
        'resume' => ['resume', 'résumé', 'cv', 'curriculum'],
        'education' => ['educat*', 'degree*', 'school*', 'college', 'universit*', 'studied', 'graduat*', 'bachelor*'],
        'certificates' => ['certif*', 'training*', 'course*', 'credential*'],
        'experience' => ['experience*', 'years', 'work history', 'worked', 'career', 'compan*', 'employ*', 'previous', 'background', 'role*'],
        'projects' => ['project*', 'portfolio', 'built', 'build', 'case stud*', 'system*', 'app', 'apps', 'application*', 'work samples', 'showcase'],
        'services' => ['service*', 'offer*', 'help', 'do for', 'what can you'],
        'skills' => ['skill*', 'stack*', 'tech', 'technolog*', 'language*', 'framework*', 'tool*', 'know', 'proficien*', 'expert*'],
        'location' => ['where', 'located', 'location', 'based', 'live', 'timezone', 'time zone', 'country', 'philippines', 'remote'],
        'about' => ['about you', 'who are you', 'yourself', 'introduce', 'tell me about you', 'who is'],
        'greeting' => ['hi', 'hello', 'hey', 'good morning', 'good afternoon', 'good evening', 'kumusta', 'musta'],
        'thanks' => ['thanks', 'thank you', 'salamat', 'ty'],
    ];

    private const DEFAULT_SUGGESTIONS = [
        'What is your tech stack?',
        'Are you available for work?',
        'Show me your projects',
        'How can I contact you?',
    ];

    public function __construct(
        private readonly GetProfileAction $getProfile,
    ) {}

    public function execute(string $question): AssistantReply
    {
        $text = $this->normalize($question);
        $profile = $this->getProfile->execute();

        $skills = $this->mentionedTags($text);
        $project = $this->mentionedProject($text);
        $role = $this->mentionedRole($text);
        $topic = $this->topic($text);

        return match (true) {
            $project !== null && ! in_array($topic, ['rates', 'contact'], true) => $this->project($project),
            $role !== null && in_array($topic, [null, 'experience', 'projects', 'skills'], true) => $this->role($role),
            $skills->isNotEmpty() && ! in_array($topic, ['rates', 'contact', 'availability', 'location'], true) => $this->skills($skills),
            $topic === 'skills' && $this->asksAboutSpecificThing($text) => $this->unknownSkill(),
            $topic === 'rates' => $this->rates($profile),
            $topic === 'availability' => $this->availability($profile),
            $topic === 'contact' => $this->contact($profile),
            $topic === 'location' => $this->location($profile),
            $topic === 'resume' => $this->resume($profile),
            $topic === 'education' => $this->education(),
            $topic === 'certificates' => $this->certificates(),
            $topic === 'experience' => $this->experience(),
            $topic === 'projects' => $this->projects(),
            $topic === 'services' => $this->services(),
            $topic === 'skills' => $this->skillSummary(),
            $topic === 'about' => $this->about($profile),
            $topic === 'greeting' => $this->greeting($profile),
            $topic === 'thanks' => new AssistantReply('thanks', ['You are welcome! Anything else you would like to know?'], suggestions: self::DEFAULT_SUGGESTIONS),
            default => $this->fallback(),
        };
    }

    private function normalize(string $question): string
    {
        $text = Str::of($question)->lower()->squish()->toString();

        return ' '.preg_replace('/[^\p{L}\p{N}.#+\-\s]/u', ' ', $text).' ';
    }

    private function topic(string $text): ?string
    {
        $best = null;
        $bestScore = 0;

        foreach (self::TOPICS as $topic => $patterns) {
            $score = 0;

            foreach ($patterns as $pattern) {
                $regex = str_ends_with($pattern, '*')
                    ? preg_quote(substr($pattern, 0, -1), '/').'[\p{L}\p{N}]*'
                    : preg_quote($pattern, '/');
                $score += (int) preg_match_all('/(?<![\p{L}\p{N}])'.$regex.'(?![\p{L}\p{N}])/u', $text);
            }

            if ($score > $bestScore) {
                $best = $topic;
                $bestScore = $score;
            }
        }

        return $best;
    }

    /**
     * "Do you know Go?" style questions, for when no known skill was mentioned.
     */
    private function asksAboutSpecificThing(string $text): bool
    {
        return (bool) preg_match('/\b(do you (know|use|work with|have experience)|experience (with|in)|familiar with|can you (use|code|work))\b/u', $text);
    }

    /**
     * Tags the question names, matched on their name and common short forms (react.js → react, html5 → html).
     *
     * @return Collection<int, Tag>
     */
    private function mentionedTags(string $text): Collection
    {
        $aliases = ['js' => 'javascript', 'ts' => 'typescript', 'vue' => 'vue.js', 'tailwind' => 'tailwind css', 'filament' => 'filament', 'copilot' => 'github copilot'];

        $matched = Tag::query()->get()->filter(function (Tag $tag) use ($text, $aliases): bool {
            $name = mb_strtolower($tag->name);
            $forms = array_unique(array_filter([
                $name,
                preg_replace('/\.js$/', '', $name),
                preg_replace('/\d+$/', '', $name),
                ...array_keys(array_filter($aliases, fn (string $target): bool => $target === $name)),
            ], fn (?string $form): bool => $form !== null && mb_strlen($form) >= 2));

            foreach ($forms as $form) {
                if (preg_match('/(?<![\p{L}\p{N}])'.preg_quote($form, '/').'(?![\p{L}\p{N}])/u', $text)) {
                    return true;
                }
            }

            return false;
        });

        // "Claude Code" also matches "Claude": keep the most specific tag.
        return $matched
            ->reject(fn (Tag $tag): bool => $matched->contains(fn (Tag $other): bool => $other->isNot($tag)
                && str_contains(mb_strtolower($other->name), mb_strtolower($tag->name))))
            ->take(3)
            ->values();
    }

    /**
     * A project the question names: its bracketed acronym ("DTS"), all of its distinctive title words,
     * any two of them, or one long word no other project title uses ("profiling").
     */
    private function mentionedProject(string $text): ?Project
    {
        $projects = Project::query()->published()->ordered()->get();
        $common = ['system', 'site', 'management', 'national', 'information', 'landing', 'directory'];
        $wordsOf = fn (Project $project): Collection => collect(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($project->title)) ?: [])
            ->filter(fn (string $word): bool => mb_strlen($word) >= 4 && ! in_array($word, $common, true))
            ->values();
        $frequency = $projects->flatMap($wordsOf)->countBy();
        $has = fn (string $word): bool => (bool) preg_match('/(?<![\p{L}\p{N}])'.preg_quote($word, '/').'(?![\p{L}\p{N}])/u', $text);

        return $projects->first(function (Project $project) use ($wordsOf, $frequency, $has): bool {
            if (preg_match('/\(([A-Za-z]{2,})\)/', $project->title, $match) && $has(mb_strtolower($match[1]))) {
                return true;
            }

            $words = $wordsOf($project);
            $present = $words->filter($has);

            return ($words->isNotEmpty() && $present->count() === $words->count())
                || $present->count() >= 2
                || $present->contains(fn (string $word): bool => mb_strlen($word) >= 7 && $frequency->get($word) === 1);
        });
    }

    private function mentionedRole(string $text): ?Experience
    {
        // Confidential roles are never looked up by company name, so their client stays private.
        return Experience::query()->ordered()->where('is_confidential', false)->get()->first(function (Experience $role) use ($text): bool {
            $company = mb_strtolower((string) $role->company);
            $first = strtok($company, ' ');

            return $company !== '' && (str_contains($text, $company) || ($first !== false && mb_strlen($first) >= 4 && str_contains($text, " {$first} ")));
        });
    }

    /**
     * @param  Collection<int, Tag>  $tags
     */
    private function skills(Collection $tags): AssistantReply
    {
        $paragraphs = [];
        $bullets = [];

        foreach ($tags as $tag) {
            $projects = $tag->projects()->published()->ordered()->pluck('title');
            $roles = Experience::query()->ordered()->get()
                ->filter(fn (Experience $role): bool => in_array(mb_strtolower($tag->name), array_map('mb_strtolower', $role->technologies ?? []), true));

            $paragraphs[] = $tag->category !== null
                ? "Yes, {$tag->name} is one of my skills ({$tag->category->label()})."
                : "{$tag->name} is part of my project work.";

            foreach ($roles as $role) {
                $bullets[] = "Used as {$this->roleName($role)}";
            }

            foreach ($projects as $title) {
                $bullets[] = "Used in {$title}";
            }
        }

        return new AssistantReply(
            'skills',
            $paragraphs,
            array_values(array_unique($bullets)),
            [['label' => 'All projects', 'href' => route('projects.index', absolute: false)]],
            ['What other skills do you have?', 'Show me your projects'],
        );
    }

    private function unknownSkill(): AssistantReply
    {
        return new AssistantReply(
            'unknown_skill',
            ['That one is not listed among my skills, so I would rather not claim it. Here is what I do work with:'],
            $this->skillLines(),
            [['label' => 'Ask me directly', 'href' => route('contact.show', absolute: false)]],
            ['Show me your projects', 'How can I contact you?'],
        );
    }

    private function skillSummary(): AssistantReply
    {
        return new AssistantReply(
            'skills',
            ['Here is my stack:'],
            $this->skillLines(),
            [['label' => 'Background', 'href' => route('about', absolute: false)]],
            ['Do you know React?', 'Show me your projects'],
        );
    }

    /**
     * @return array<int, string>
     */
    private function skillLines(): array
    {
        return Tag::query()->skills()->get()
            ->groupBy(fn (Tag $tag): string => (string) $tag->category?->label())
            ->map(fn (Collection $tags, string $category): string => "{$category}: ".$tags->pluck('name')->implode(', '))
            ->values()
            ->all();
    }

    private function project(Project $project): AssistantReply
    {
        $links = [['label' => 'Read the case study', 'href' => route('projects.show', $project->slug, absolute: false)]];

        if ($project->live_url !== null) {
            $links[] = ['label' => 'Live site', 'href' => $project->live_url];
        }

        $stack = $project->tags()->pluck('name');

        return new AssistantReply(
            'project',
            array_filter([
                "{$project->title}: {$project->summary}",
                $stack->isNotEmpty() ? 'Built with '.$stack->implode(', ').'.' : null,
            ]),
            links: $links,
            suggestions: ['Show me your other projects', 'What is your tech stack?'],
        );
    }

    private function projects(): AssistantReply
    {
        $projects = Project::query()->published()->ordered()->get();

        if ($projects->isEmpty()) {
            return $this->fallback();
        }

        return new AssistantReply(
            'projects',
            ['Here are the projects on this site:'],
            $projects->map(fn (Project $project): string => "{$project->title}: {$project->summary}")->values()->all(),
            [['label' => 'All projects', 'href' => route('projects.index', absolute: false)]],
            ['Tell me about the TESDA Profiling MIS', 'What is your tech stack?'],
        );
    }

    private function role(Experience $role): AssistantReply
    {
        return new AssistantReply(
            'role',
            [$this->roleName($role).', '.$this->period($role->started_on->format('M Y'), $role->ended_on?->format('M Y')).'.'],
            $role->highlights ?? [],
            [['label' => 'Full background', 'href' => route('about', absolute: false)]],
            ['What other roles have you had?', 'Show me your projects'],
        );
    }

    private function experience(): AssistantReply
    {
        $roles = Experience::query()->ordered()->get();
        $profile = $this->getProfile->execute();

        return new AssistantReply(
            'experience',
            array_values(array_filter([$profile->short_bio, 'My roles:'])),
            $roles->map(fn (Experience $role): string => $this->roleName($role).' ('.$this->period($role->started_on->format('M Y'), $role->ended_on?->format('M Y')).')')->values()->all(),
            [['label' => 'Full background', 'href' => route('about', absolute: false)]],
            ['Tell me about your work at TESDA', 'What is your education?'],
        );
    }

    private function education(): AssistantReply
    {
        return new AssistantReply(
            'education',
            ['My education:'],
            Education::query()->ordered()->get()->map(fn (Education $entry): string => "{$entry->qualification}, {$entry->institution} ({$this->period($entry->started_on->format('Y'), $entry->ended_on?->format('Y'))})")->values()->all(),
            [['label' => 'About me', 'href' => route('about', absolute: false)]],
            ['Do you have certifications?', 'What is your experience?'],
        );
    }

    private function certificates(): AssistantReply
    {
        $certificates = Certificate::query()->ordered()->get();

        if ($certificates->isEmpty()) {
            return new AssistantReply('certificates', ['There are no certifications listed on this site yet.'], suggestions: self::DEFAULT_SUGGESTIONS);
        }

        return new AssistantReply(
            'certificates',
            ['Training and certifications:'],
            $certificates->map(fn (Certificate $certificate): string => $certificate->name.($certificate->issuer ? ", {$certificate->issuer}" : '').' ('.$certificate->issued_on->format('M Y').')')->values()->all(),
            [['label' => 'About me', 'href' => route('about', absolute: false)]],
            ['What is your education?'],
        );
    }

    private function services(): AssistantReply
    {
        $services = Service::query()->ordered()->get();

        if ($services->isEmpty()) {
            return $this->skillSummary();
        }

        return new AssistantReply(
            'services',
            ['What I can help with:'],
            $services->map(fn (Service $service): string => "{$service->title}: {$service->description}")->values()->all(),
            [['label' => 'Services', 'href' => route('services', absolute: false)], ['label' => 'Send a brief', 'href' => route('contact.show', absolute: false)]],
            ['Are you available for work?', 'What are your rates?'],
        );
    }

    private function availability(Profile $profile): AssistantReply
    {
        return new AssistantReply(
            'availability',
            array_filter([
                $profile->availability_status->label().($profile->availability ? " · {$profile->availability}" : '').'.',
                $profile->working_hours ? "Working hours: {$profile->working_hours}." : null,
            ]),
            links: [['label' => 'Send a project brief', 'href' => route('contact.show', absolute: false)]],
            suggestions: ['What are your rates?', 'What services do you offer?'],
        );
    }

    private function rates(Profile $profile): AssistantReply
    {
        return new AssistantReply(
            'rates',
            [$profile->starting_rate
                ? "Rates start at {$profile->starting_rate}. The final price depends on the scope."
                : 'Rates depend on the scope of the work. Send a short brief with your budget, in pesos, dollars or another currency, and I will reply with an estimate.'],
            links: [['label' => 'Send a project brief', 'href' => route('contact.show', absolute: false)]],
            suggestions: ['Are you available for work?', 'What services do you offer?'],
        );
    }

    private function contact(Profile $profile): AssistantReply
    {
        $links = [['label' => 'Contact form', 'href' => route('contact.show', absolute: false)]];

        if ($profile->email) {
            $links[] = ['label' => $profile->email, 'href' => "mailto:{$profile->email}"];
        }

        if ($profile->booking_url) {
            $links[] = ['label' => 'Book a call', 'href' => $profile->booking_url];
        }

        return new AssistantReply(
            'contact',
            array_filter([
                'The best way is a short project brief through the contact form'.($profile->email ? ", or email me at {$profile->email}" : '').'.',
                $profile->response_time ? "I usually reply {$profile->response_time}." : null,
            ]),
            links: $links,
            suggestions: ['Are you available for work?', 'Where are you based?'],
        );
    }

    private function location(Profile $profile): AssistantReply
    {
        if (! $profile->location) {
            return $this->fallback();
        }

        return new AssistantReply(
            'location',
            ['I am based in '.$profile->location.($profile->timezone ? ", {$profile->timezone}" : '').'.'],
            suggestions: ['Are you available for work?', 'How can I contact you?'],
        );
    }

    private function resume(Profile $profile): AssistantReply
    {
        if ($profile->resume_path === null) {
            return new AssistantReply(
                'resume',
                ['My résumé is not uploaded here yet, but my full background is on the About page.'],
                links: [['label' => 'About me', 'href' => route('about', absolute: false)]],
            );
        }

        return new AssistantReply(
            'resume',
            ['You can download my résumé as a PDF.'],
            links: [['label' => 'Download résumé', 'href' => route('resume.download', absolute: false)]],
            suggestions: ['What is your experience?'],
        );
    }

    private function about(Profile $profile): AssistantReply
    {
        return new AssistantReply(
            'about',
            array_filter(["I'm {$profile->name}, {$profile->headline}.", $profile->short_bio]),
            links: [['label' => 'About me', 'href' => route('about', absolute: false)]],
            suggestions: self::DEFAULT_SUGGESTIONS,
        );
    }

    private function greeting(Profile $profile): AssistantReply
    {
        return new AssistantReply(
            'greeting',
            ["Hi! I can answer questions about {$profile->name}'s skills, projects, experience and availability."],
            suggestions: self::DEFAULT_SUGGESTIONS,
        );
    }

    private function fallback(): AssistantReply
    {
        return new AssistantReply(
            'fallback',
            ["Sorry, I don't have an answer for that. I can only answer from what is on this site. You can ask about my skills, projects, experience or availability, or ask me directly."],
            links: [['label' => 'Contact form', 'href' => route('contact.show', absolute: false)]],
            suggestions: self::DEFAULT_SUGGESTIONS,
        );
    }

    private function roleName(Experience $role): string
    {
        return $role->role.' at '.($role->is_confidential || $role->company === null ? 'a confidential client' : $role->company);
    }

    private function period(?string $start, ?string $end): string
    {
        return ($start ?? '?').' – '.($end ?? 'Present');
    }
}
