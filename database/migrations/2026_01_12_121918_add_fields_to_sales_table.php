<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('vat', 10, 2)->default(0)->after('total');
            $table->decimal('tax', 10, 2)->default(0)->after('vat');
            $table->decimal('delivery_charge', 10, 2)->default(0)->after('tax');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['vat', 'tax', 'delivery_charge']);
        });
    }
};
