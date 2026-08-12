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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->integer('role_id')->default(1);
            $table->string('images')->nullable();
            $table->integer('verification_code')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->bigInteger('billing_address')->nullable();
            $table->bigInteger('shipping_address')->nullable();
            $table->enum('is_guest', [0, 1])->default(0);
            $table->enum('status', [0, 1])->default(1);
            $table->enum('type', [1, 2, 3, 4])->default(1); // 1=admin, 2=user, 3=manager, 4=staff
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
