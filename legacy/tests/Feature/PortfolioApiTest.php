<?php

namespace Tests\Feature;

use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PortfolioSeeder::class);
    }

    public function test_portfolio_api_returns_complete_data_structure(): void
    {
        $response = $this->getJson('/api/v1/portfolio');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'settings',
                'projects',
                'skills',
                'experiences',
                'education',
                'certificates',
                'services',
                'blogs',
                'social_links',
            ]);
    }

    public function test_ai_rag_chat_endpoint_returns_response_and_actions(): void
    {
        $response = $this->postJson('/api/v1/ai/chat', [
            'message' => 'What is Cedric\'s background with Laravel and AI?',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'session_id',
                'reply',
                'suggested_actions',
            ]);
    }

    public function test_contact_submission_stores_message(): void
    {
        $response = $this->postJson('/api/v1/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Hiring Architect',
            'message' => 'We would love to discuss a senior architecture role.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_github_stats_returns_metrics(): void
    {
        $response = $this->getJson('/api/v1/github/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'username',
                'public_repos',
                'total_stars',
                'top_languages',
            ]);
    }

    public function test_resume_download_returns_pdf_document(): void
    {
        $response = $this->get('/api/v1/resume/download');

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }
}
