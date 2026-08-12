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
        if (!Schema::hasTable('advance_salaries')) {
            Schema::create('advance_salaries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->decimal('amount', 10, 2);
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->date('request_date');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_salaries');
    }
};