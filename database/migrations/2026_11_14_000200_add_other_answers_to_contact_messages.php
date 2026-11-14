<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The sender's own words when they pick "other" for the project type, budget or timeline.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('project_type_other', 150)->nullable()->after('project_type');
            $table->string('budget_other', 150)->nullable()->after('budget_currency');
            $table->string('timeline_other', 150)->nullable()->after('timeline');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['project_type_other', 'budget_other', 'timeline_other']);
        });
    }
};
