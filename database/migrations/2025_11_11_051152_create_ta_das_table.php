<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('ta_das', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id')->nullable();
    $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
    $table->date('date');
    $table->decimal('amount', 10, 2);
    $table->decimal('used_amount', 10, 2)->default(0); 
    $table->decimal('remaining_amount', 10, 2)->default(0);
    $table->text('purpose')->nullable();
    $table->enum('type', ['TA', 'DA'])->default('TA');
    $table->enum('payment_type', ['Advance', 'Claim'])->default('Advance');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('ta_das');
    }
};