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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->date('date')->index();
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('attendance_status')->default('1'); // 1 = present, 2 = absent, 3 = leave
            $table->enum('status', ['0','1'])->default('1'); // 1 = active, 0 = inactive
            $table->text('remarks')->nullable();
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
