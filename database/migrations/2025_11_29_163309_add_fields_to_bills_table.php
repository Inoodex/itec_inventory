<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            if (!Schema::hasColumn('bills', 'bank_detail_id')) {
                $table->foreignId('bank_detail_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('bills', 'company_detail_id')) {
                $table->foreignId('company_detail_id')->nullable()->constrained()->onDelete('set null');
            }
            if (!Schema::hasColumn('bills', 'terms_conditions')) {
                $table->text('terms_conditions')->nullable();
            }
            if (!Schema::hasColumn('bills', 'subject')) {
                $table->string('subject')->nullable();
            }
            if (!Schema::hasColumn('bills', 'attention_to')) {
                $table->string('attention_to')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            //
        });
    }
};
