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
            $table->boolean('show_signature')->default(true)->after('total_amount');
            $table->boolean('show_seal')->default(true)->after('show_signature');
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->boolean('show_signature')->default(true)->after('subject');
            $table->boolean('show_seal')->default(true)->after('show_signature');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->boolean('show_signature')->default(true)->after('total_amount');
            $table->boolean('show_seal')->default(true)->after('show_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['show_signature', 'show_seal']);
        });

        Schema::table('challans', function (Blueprint $table) {
            $table->dropColumn(['show_signature', 'show_seal']);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['show_signature', 'show_seal']);
        });
    }
};
