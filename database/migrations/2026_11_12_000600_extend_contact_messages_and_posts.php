<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Contact messages get structured brief fields and store a keyed hash of the sender's IP instead of
 * the raw IP and user agent. Posts get SEO overrides.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['subject', 'ip_address', 'user_agent']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('project_type', 30)->nullable()->after('email');
            $table->string('budget', 20)->nullable()->after('project_type');
            $table->string('timeline', 30)->nullable()->after('budget');
            $table->string('ip_hash', 64)->nullable()->after('message');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('seo_title', 70)->nullable()->after('published_at');
            $table->string('seo_description', 200)->nullable()->after('seo_title');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['project_type', 'budget', 'timeline', 'ip_hash']);
        });

        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('subject', 150)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 500)->nullable();
        });
    }
};
