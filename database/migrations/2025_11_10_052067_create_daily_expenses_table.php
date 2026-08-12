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
        if (!Schema::hasTable('daily_expenses')) {
            Schema::create('daily_expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
                $table->date('date')->nullable(false);
                $table->foreignId('expense_category_id')->constrained('expense_categories')->onDelete('cascade');
                $table->decimal('amount', 15, 2)->nullable(false);
                $table->enum('spend_method', ['cash', 'card', 'bank_transfer'])->nullable(false);
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_expenses');
    }
};