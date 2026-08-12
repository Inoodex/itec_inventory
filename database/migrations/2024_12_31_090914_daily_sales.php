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
        Schema::create('daily_sales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description')->nullable();
            $table->decimal('card_amount')->nullable();
            $table->decimal('cash_amount')->nullable();
            $table->decimal('others_amount')->nullable();
            $table->decimal('total_amount')->nullable();
            $table->text('assigned_person_id');
            $table->enum('status', [0, 1])->default(1);
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
