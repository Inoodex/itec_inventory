<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->string('reference_number')->nullable();
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['sale', 'project']);
            
            $table->string('work_order_number')->nullable();
            
            $table->date('bill_date');
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bills');
    }
};