<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('company')->nullable()->change();
            $table->boolean('is_confidential')->default(false)->after('company_url');
            $table->boolean('is_remote')->default(false)->after('location');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->string('badge_path')->nullable()->after('credential_url');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn('badge_path');
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn(['is_confidential', 'is_remote']);
            $table->string('company')->nullable(false)->change();
        });
    }
};
