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
     Schema::create('bank_details', function (Blueprint $table) {
    $table->id();
    $table->string('account_name');
    $table->string('bank_name');
    $table->string('branch');
    $table->string('account_number');
    $table->string('account_type'); 
    $table->string('routing_number')->nullable();
    $table->boolean('is_default')->default(false);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_details');
    }
};
