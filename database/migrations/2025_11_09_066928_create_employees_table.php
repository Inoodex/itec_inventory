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
        Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->string('employee_id')->unique();
    $table->string('name');
    $table->string('email')->nullable()->unique();
    $table->string('phone')->nullable();
    $table->string('image')->nullable();
    $table->string('designation')->nullable();
    $table->date('join_date')->nullable();
    $table->decimal('salary', 12, 2)->default(0);
    $table->string('status')->default('active');
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};