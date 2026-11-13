<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Turns the profile into the site-wide settings singleton. Existing columns keep their meaning:
 * `headline` is the role title, `tagline` the value statement, `bio` the long bio and
 * `availability` the availability note.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('short_bio', 500)->nullable()->after('tagline');
            $table->string('timezone', 100)->nullable()->after('location');
            $table->string('availability_status', 20)->default('available')->after('phone');
            $table->string('working_hours', 200)->nullable()->after('availability');
            $table->string('response_time', 100)->nullable()->after('working_hours');
            $table->string('booking_url')->nullable()->after('response_time');
            $table->string('currently_learning', 500)->nullable()->after('booking_url');
            $table->string('starting_rate', 100)->nullable()->after('currently_learning');
            $table->json('engagement_types')->nullable()->after('starting_rate');
            $table->json('process_steps')->nullable()->after('engagement_types');
            $table->boolean('blog_enabled')->default(true)->after('process_steps');
            $table->string('seo_title', 70)->nullable()->after('blog_enabled');
            $table->string('seo_description', 200)->nullable()->after('seo_title');
            $table->string('og_image_path')->nullable()->after('seo_description');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'short_bio', 'timezone', 'availability_status', 'working_hours', 'response_time', 'booking_url',
                'currently_learning', 'starting_rate', 'engagement_types', 'process_steps', 'blog_enabled',
                'seo_title', 'seo_description', 'og_image_path',
            ]);
        });
    }
};
