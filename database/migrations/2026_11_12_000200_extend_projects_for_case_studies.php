<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces the free-form project body with structured case-study fields.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['body', 'period', 'highlights']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->text('problem')->nullable()->after('summary');
            $table->text('role_details')->nullable()->after('role');
            $table->longText('architecture')->nullable()->after('role_details');
            $table->json('architecture_tree')->nullable()->after('architecture');
            $table->string('diagram_path')->nullable()->after('architecture_tree');
            $table->text('decisions')->nullable()->after('diagram_path');
            $table->text('challenges')->nullable()->after('decisions');
            $table->text('results')->nullable()->after('challenges');
            $table->json('metrics')->nullable()->after('results');
            $table->string('demo_url')->nullable()->after('repo_url');
            $table->boolean('is_confidential')->default(false)->after('demo_url');
            $table->date('started_on')->nullable()->after('is_confidential');
            $table->date('ended_on')->nullable()->after('started_on');
            $table->string('seo_title', 70)->nullable()->after('published_at');
            $table->string('seo_description', 200)->nullable()->after('seo_title');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'problem', 'role_details', 'architecture', 'architecture_tree', 'diagram_path', 'decisions',
                'challenges', 'results', 'metrics', 'demo_url', 'is_confidential', 'started_on', 'ended_on',
                'seo_title', 'seo_description',
            ]);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->longText('body')->nullable();
            $table->string('period', 50)->nullable();
            $table->json('highlights')->nullable();
        });
    }
};
