<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Uploaded files kept in the database (the `database` media disk), for hosts without a persistent
 * filesystem such as Vercel. Contents are stored base64-encoded so the column works the same on
 * Postgres, MySQL and SQLite.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stored_files', function (Blueprint $table) {
            $table->id();
            $table->string('path', 512)->unique();
            $table->string('mime_type', 150)->nullable();
            $table->unsignedBigInteger('size');
            $table->longText('contents');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stored_files');
    }
};
