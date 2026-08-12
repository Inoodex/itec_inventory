<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->decimal('budget', 12, 2)->nullable();
            $table->decimal('sub_total', 12, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->decimal('advanced_payment', 12, 2)->default(0);
            $table->decimal('due_payment', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
}