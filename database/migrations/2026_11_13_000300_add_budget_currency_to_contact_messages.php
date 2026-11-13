<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Budgets can be stated in the client's own currency (PHP, USD, EUR, …).
 * Existing briefs were all in USD.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->string('budget_currency', 3)->nullable()->after('budget');
        });

        DB::table('contact_messages')->whereNotNull('budget')->update(['budget_currency' => 'USD']);
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn('budget_currency');
        });
    }
};
