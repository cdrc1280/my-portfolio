<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Testimonials, services, "how I work" approaches and the /uses list.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author_name', 100);
            $table->string('author_role', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('photo_path')->nullable();
            $table->text('quote');
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('has_permission')->default(false)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description');
            $table->string('icon', 60)->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('work_approaches', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description');
            $table->string('snippet', 200)->nullable();
            $table->string('icon', 60)->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('uses_items', function (Blueprint $table) {
            $table->id();
            $table->string('category', 60)->index();
            $table->string('name', 100);
            $table->string('description', 500)->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uses_items');
        Schema::dropIfExists('work_approaches');
        Schema::dropIfExists('services');
        Schema::dropIfExists('testimonials');
    }
};
