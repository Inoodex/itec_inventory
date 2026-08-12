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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_code', 50)->unique();
            $table->string('account_name');
            $table->enum('account_type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->unsignedInteger('level')->default(1);
            $table->foreignId('bank_detail_id')->nullable()->constrained('bank_details')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->decimal('opening_balance', 15, 2)->default(0.00);
            $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['account_type', 'is_active']);
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
