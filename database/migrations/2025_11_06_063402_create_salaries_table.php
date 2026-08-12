<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    if (!Schema::hasTable('salaries')) {
        Schema::create('salaries', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('employee_id');
        $table->string('month'); 
        $table->decimal('basic_salary', 10, 2);
        $table->decimal('advance', 10, 2)->nullable();
        $table->decimal('allowance', 10, 2)->nullable();
        $table->decimal('deduction', 10, 2)->nullable();
        $table->decimal('net_salary', 10, 2);
        $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
        $table->date('payment_date')->nullable();
        $table->text('note')->nullable();
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
        Schema::dropIfExists('salaries');
    }
};