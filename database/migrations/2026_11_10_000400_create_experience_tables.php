<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('company_url')->nullable();
            $table->string('role');
            $table->string('location')->nullable();
            $table->string('employment_type', 20);
            $table->date('started_on');
            $table->date('ended_on')->nullable();
            $table->text('summary')->nullable();
            $table->json('highlights')->nullable();
            $table->json('technologies')->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('education', function (Blueprint $table) {
            $table->id();
            $table->string('institution');
            $table->string('qualification');
            $table->string('field_of_study')->nullable();
            $table->date('started_on');
            $table->date('ended_on')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('issuer');
            $table->date('issued_on');
            $table->string('credential_url')->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('education');
        Schema::dropIfExists('experiences');
    }
};
