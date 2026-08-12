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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->double('qty');
            $table->double('total');
            $table->double('payble');
            $table->double('bill');
            $table->decimal('advanced_payment', 15, 2)->nullable();
            $table->decimal('due_payment', 15, 2)->nullable();
            $table->double('discount')->nullable();
            $table->string('sales_by')->nullable();
            $table->enum('status', ['paid', 'partial', 'credit'])->default('credit');
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