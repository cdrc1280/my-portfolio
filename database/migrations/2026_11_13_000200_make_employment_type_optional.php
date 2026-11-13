<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A role's employment type isn't always stated, and showing a guessed one would be inaccurate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('employment_type', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('employment_type', 20)->nullable(false)->change();
        });
    }
};
