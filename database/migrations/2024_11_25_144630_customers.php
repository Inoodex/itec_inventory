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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country_code')->nullable();
            $table->string('phone');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->text('address')->nullable();
            $table->string('images')->nullable();
            $table->integer('verification_code')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->bigInteger('billing_address')->nullable();
            $table->bigInteger('shipping_address')->nullable();
            $table->enum('status', [0, 1])->default(1);
            $table->rememberToken();
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
