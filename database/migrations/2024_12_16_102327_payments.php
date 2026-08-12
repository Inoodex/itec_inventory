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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_for');
            $table->bigInteger('customer_id');
            $table->bigInteger('sale_id');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer'])->default('cash');
            $table->double('amount');
            $table->enum('status', [0, 1])->default(1);
            $table->timestamps();
        });
        /*

        ALTER TABLE `services` ADD `paid_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `bill`, ADD `due_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `paid_amount`; 
        ALTER TABLE `sales` ADD `paid_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `bill`, ADD `due_amount` DOUBLE NOT NULL DEFAULT '0' AFTER `paid_amount`;

        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
