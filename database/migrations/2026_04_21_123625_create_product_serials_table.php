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
        if (!Schema::hasTable('product_serials')) {
            Schema::create('product_serials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('purchase_id')->nullable()->constrained()->onDelete('set null');
                $table->unsignedBigInteger('sales_item_id')->nullable(); 
                $table->string('serial_number')->unique();
                $table->enum('status', ['available', 'sold', 'damaged', 'returned'])->default('available');
                $table->timestamps();

                $table->foreign('sales_item_id')->references('id')->on('sales_items')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_serials');
    }
};
