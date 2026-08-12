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
      Schema::create('challan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challan_id');
            $table->text('description');
            $table->integer('quantity')->default(1);
            $table->string('unit')->default('Piece');
            $table->timestamps();
            
            $table->foreign('challan_id')->references('id')->on('challans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challan_items');
    }
};