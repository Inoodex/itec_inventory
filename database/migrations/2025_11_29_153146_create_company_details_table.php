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
       Schema::create('company_details', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('signatory_name');
    $table->string('signatory_designation');
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->string('website')->nullable();
    $table->text('address')->nullable();
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
        Schema::dropIfExists('company_details');
    }
};
