<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Project Categories
        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color')->default('#3b82f6');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->longText('description')->nullable();
            $table->string('hero_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('github_url')->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('timeline')->nullable();
            $table->json('features')->nullable();
            $table->text('architecture')->nullable();
            $table->text('challenges')->nullable();
            $table->text('solutions')->nullable();
            $table->text('lessons_learned')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('stars_count')->default(0);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Skill Categories
        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Skills
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->integer('proficiency')->default(90); // 1-100
            $table->float('orbit_radius')->default(3.5); // for 3D sphere orbit
            $table->string('color')->default('#00f0ff');
            $table->integer('years_of_experience')->default(5);
            $table->integer('projects_count')->default(10);
            $table->boolean('is_featured')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Experiences
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('position');
            $table->string('location')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('summary')->nullable();
            $table->json('responsibilities')->nullable();
            $table->json('achievements')->nullable();
            $table->json('skills_used')->nullable();
            $table->string('logo')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Education
        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->string('institution');
            $table->string('degree');
            $table->string('field_of_study');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('grade')->nullable();
            $table->json('achievements')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Certificates
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('issuer');
            $table->date('issue_date');
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->string('image')->nullable();
            $table->string('category')->default('Architecture');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Services
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_description');
            $table->longText('description')->nullable();
            $table->string('icon')->default('code');
            $table->json('features')->nullable();
            $table->string('badge')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Testimonials
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('position');
            $table->string('company');
            $table->string('avatar')->nullable();
            $table->text('content');
            $table->integer('rating')->default(5);
            $table->boolean('is_featured')->default(true);
            $table->timestamps();
        });

        // FAQs
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('General');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Timeline Events
        Schema::create('timeline_events', function (Blueprint $table) {
            $table->id();
            $table->string('year');
            $table->string('title');
            $table->text('description');
            $table->string('category')->default('Career');
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Blog Categories & Blogs
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary');
            $table->longText('content');
            $table->string('cover_image')->nullable();
            $table->integer('reading_time')->default(5);
            $table->json('tags')->nullable();
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Social Links
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform');
            $table->string('url');
            $table->string('icon');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Site Settings
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        // AI Knowledge Base
        Schema::create('ai_knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->string('title');
            $table->string('category')->default('General');
            $table->longText('content');
            $table->json('metadata')->nullable();
            $table->json('embedding')->nullable();
            $table->timestamps();
        });

        // AI Conversations & Messages
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('title')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ai_conversation_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant', 'system']);
            $table->longText('content');
            $table->json('suggested_actions')->nullable();
            $table->timestamps();
        });

        // Contact Messages
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status')->default('unread'); // unread, read, replied
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_knowledge_bases');
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('social_links');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_categories');
        Schema::dropIfExists('timeline_events');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('services');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('education');
        Schema::dropIfExists('experiences');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_categories');
    }
};
