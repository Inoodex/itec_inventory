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
        if (!Schema::hasColumn('sales', 'deleted_at')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('purchases', 'deleted_at')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('projects', 'deleted_at')) {
            Schema::table('projects', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('products', 'deleted_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
