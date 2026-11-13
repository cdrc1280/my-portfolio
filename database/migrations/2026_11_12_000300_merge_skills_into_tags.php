<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tags now double as skills: a category puts a tag in the public skills section.
 * The separate skills / skill_categories tables are removed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->string('category', 30)->nullable()->after('slug')->index();
            $table->string('icon', 60)->nullable()->after('category');
            $table->unsignedInteger('position')->default(0)->after('icon')->index();
        });

        Schema::dropIfExists('skills');
        Schema::dropIfExists('skill_categories');
    }

    public function down(): void
    {
        Schema::create('skill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_category_id')->constrained()->restrictOnDelete();
            $table->string('name', 60);
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
            $table->unique(['skill_category_id', 'name']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['position']);
            $table->dropColumn(['category', 'icon', 'position']);
        });
    }
};
