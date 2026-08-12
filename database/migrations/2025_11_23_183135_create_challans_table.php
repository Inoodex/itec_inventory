<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_number')->unique();
            $table->string('reference_number');
            $table->date('challan_date');
            $table->enum('type', ['sale', 'project']);
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            
            
            $table->timestamps();
            
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
        
        
    }

    public function down()
    {
        Schema::dropIfExists('challans');
    }
};