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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('alternative_phone')->nullable();
            $table->bigInteger('state');
            $table->bigInteger('area');
            $table->text('address_details');
            $table->text('comment')->nullable();
            $table->enum('address_type', [1, 2]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
