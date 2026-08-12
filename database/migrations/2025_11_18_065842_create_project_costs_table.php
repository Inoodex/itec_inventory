<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('project_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('cost_category_id')->constrained()->onDelete('cascade');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('cost_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_costs');
    }
};