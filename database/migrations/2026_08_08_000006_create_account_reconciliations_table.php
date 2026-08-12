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
        Schema::create('account_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->date('bank_statement_date');
            $table->decimal('statement_balance', 15, 2)->default(0.00);
            $table->decimal('book_balance', 15, 2)->default(0.00);
            $table->decimal('difference', 15, 2)->default(0.00);
            $table->enum('status', ['draft', 'completed'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['account_id', 'bank_statement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_reconciliations');
    }
};
