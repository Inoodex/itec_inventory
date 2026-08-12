<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenues', function (Blueprint $table) {
            $table->id();

            // Year and month of revenue
            $table->integer('year')->index();
            $table->integer('month')->index(); 

            // Financial summaries
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_purchases', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);

            // Computed: net profit/loss
            $table->decimal('net_profit', 15, 2)->virtualAs('total_sales - total_purchases - total_expenses');

            // Optional: for additional tracking
            $table->string('remarks')->nullable();

            $table->timestamps();

            $table->unique(['year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenues');
    }
};