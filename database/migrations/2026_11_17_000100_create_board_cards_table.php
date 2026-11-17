<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('board_cards', function (Blueprint $table) {
            $table->id();
            $table->string('title', 160);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('todo')->index();
            $table->string('priority', 20)->default('normal');
            $table->date('due_on')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('board_cards');
    }
};
