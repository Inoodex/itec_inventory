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
        Schema::table('sales', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('sales_by');
            $table->index('created_at');
            $table->index('status');
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('vendor_id');
            $table->index('created_at');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('daily_expenses', function (Blueprint $table) {
            $table->index('date');
            $table->index('expense_category_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->index('client_id');
            $table->index('status');
        });

        Schema::table('returns', function (Blueprint $table) {
            $table->index('sale_id');
            $table->index('customer_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['sales_by']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['vendor_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('daily_expenses', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['expense_category_id']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['client_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('returns', function (Blueprint $table) {
            $table->dropIndex(['sale_id']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['status']);
        });
    }
};
