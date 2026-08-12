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
        Schema::create('contra_entries', function (Blueprint $table) {
            $table->id();
            $table->string('contra_no', 50)->unique();
            $table->foreignId('from_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->foreignId('to_account_id')->constrained('chart_of_accounts')->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->text('description')->nullable();
            $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['date', 'from_account_id', 'to_account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contra_entries');
    }
};
