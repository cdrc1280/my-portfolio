<?php

use App\Domains\Experience\Models\Experience;
use App\Domains\Profile\Models\Profile;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    $this->seed(PortfolioSeeder::class);
});

function ask(string $question): array
{
    return test()->postJson(route('assistant.ask'), ['question' => $question])->assertOk()->json();
}

test('answers each topic from the site content', function (string $question, string $intent, ?string $mentions) {
    $reply = ask($question);

    expect($reply['intent'])->toBe($intent);

    if ($mentions !== null) {
        expect(implode(' ', [...$reply['paragraphs'], ...$reply['bullets']]))->toContain($mentions);
    }
})->with([
    'stack' => ['What is your tech stack?', 'skills', 'Laravel'],
    'known skill' => ['Do you know React?', 'skills', 'Yes, React is one of my skills'],
    'skill short form' => ['any experience with vue?', 'skills', 'Vue.js'],
    'unknown skill' => ['Do you know Rust?', 'unknown_skill', 'not listed'],
    'availability' => ['Are you available to hire?', 'availability', 'part-time contracts'],
    'rates' => ['How much do you charge per hour?', 'rates', 'pesos, dollars'],
    'contact' => ['How can I contact you?', 'contact', 'cedricjamesleala128@gmail.com'],
    'location' => ['Where are you based?', 'location', 'Taytay, Rizal'],
    'projects' => ['Show me your projects', 'projects', 'TESDA Profiling MIS'],
    'project by acronym' => ['What is the DTS?', 'project', 'Document management and monitoring system for CLGEO'],
    'project by word' => ['tell me about the profiling system', 'project', 'National profiling system'],
    'role by company' => ['What did you do at Keen Eye?', 'role', 'Front-End Developer at Keen Eye Software Solutions'],
    'experience' => ['How many years of experience do you have?', 'experience', 'IT MIS Developer'],
    'education' => ['Where did you study college? what degree', 'education', 'Bachelor of Science'],
    'certificates' => ['Any certifications?', 'certificates', 'Digital Citizenship'],
    'services' => ['What services do you offer?', 'services', 'Laravel web application development'],
    'resume' => ['Can I get your CV?', 'resume', 'résumé'],
    'greeting' => ['hello', 'greeting', null],
    'not hi in hire' => ['Can I hire you?', 'availability', null],
    'case study is not education' => ['Show me a case study', 'projects', null],
    'nonsense' => ['purple elephants dancing', 'fallback', "don't have an answer"],
]);

test('answers are plain text and never invent a rate', function () {
    $reply = ask('what are your rates?');

    expect($reply['paragraphs'][0])->not->toContain('$')->not->toContain('₱')
        ->and(json_encode($reply))->not->toContain('<');
});

test('confidential roles never reveal the client name', function () {
    Experience::factory()->create(['company' => 'Secret Corp', 'is_confidential' => true, 'role' => 'Consultant', 'position' => 0]);

    $reply = ask('What did you do at Secret Corp?');

    expect(json_encode($reply))->not->toContain('Secret Corp');
});

test('validates the question', function () {
    $this->postJson(route('assistant.ask'), ['question' => ''])->assertUnprocessable();
    $this->postJson(route('assistant.ask'), ['question' => str_repeat('a', 301)])->assertUnprocessable();
});

test('is off when switched off in settings', function () {
    Profile::query()->sole()->update(['assistant_enabled' => false]);
    cache()->flush();

    $this->postJson(route('assistant.ask'), ['question' => 'hi'])->assertNotFound();
});

test('is rate limited per visitor', function () {
    foreach (range(1, 20) as $attempt) {
        $this->postJson(route('assistant.ask'), ['question' => 'hi'])->assertOk();
    }

    $this->postJson(route('assistant.ask'), ['question' => 'hi'])->assertTooManyRequests();
});
