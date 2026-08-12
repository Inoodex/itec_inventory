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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('journal_no', 50)->unique();
            $table->date('entry_date');
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years')->nullOnDelete();
            $table->string('reference_type', 50)->default('manual'); // sale, purchase, service, project, expense, salary, return, manual, contra, opening_balance
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->decimal('total_debit', 15, 2)->default(0.00);
            $table->decimal('total_credit', 15, 2)->default(0.00);
            $table->enum('status', ['draft', 'posted', 'approved', 'reversed'])->default('posted');
            $table->foreignId('reversed_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['entry_date', 'status']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('fiscal_year_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
