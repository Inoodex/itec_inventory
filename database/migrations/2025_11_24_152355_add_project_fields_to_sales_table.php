<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('sales', function (Blueprint $table) {
        if (!Schema::hasColumn('sales', 'sale_type')) {
            $table->string('sale_type')->default('retail')->after('product_id');
        }

        if (!Schema::hasColumn('sales', 'project_id')) {
            $table->unsignedBigInteger('project_id')->nullable()->after('sale_type');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        }

        if (!Schema::hasColumn('sales', 'client_id')) {
            $table->unsignedBigInteger('client_id')->nullable()->after('customer_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
        }
    });
}


public function down()
{
    Schema::table('sales', function (Blueprint $table) {
        // Drop foreign keys first
        $table->dropForeign(['project_id']);
        $table->dropForeign(['client_id']);

        // Then drop the columns
        $table->dropColumn(['sale_type', 'project_id', 'client_id']);
    });
}

};