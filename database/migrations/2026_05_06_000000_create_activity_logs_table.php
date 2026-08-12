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
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->string('log_name')->nullable()->default('default');
                $table->string('description');
                $table->nullableMorphs('subject'); // subject_type, subject_id
                $table->nullableMorphs('causer');  // causer_type, causer_id
                $table->json('properties')->nullable();
                $table->string('event')->nullable(); // created, updated, deleted
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
