<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('address')->constrained('products')->nullOnDelete();
            }

            if (!Schema::hasColumn('services', 'paid_amount')) {
                $table->double('paid_amount')->default(0)->after('bill');
            }
        });

        DB::statement('ALTER TABLE services MODIFY country_code VARCHAR(255) NULL');
        DB::statement('ALTER TABLE services MODIFY warranty_duration INT NULL');
        DB::statement('ALTER TABLE services MODIFY repaired_by BIGINT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE services SET country_code = '+880' WHERE country_code IS NULL");
        DB::statement('UPDATE services SET warranty_duration = 0 WHERE warranty_duration IS NULL');
        DB::statement('UPDATE services SET repaired_by = 0 WHERE repaired_by IS NULL');

        DB::statement('ALTER TABLE services MODIFY country_code VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE services MODIFY warranty_duration INT NOT NULL');
        DB::statement('ALTER TABLE services MODIFY repaired_by BIGINT NOT NULL');

        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }

            if (Schema::hasColumn('services', 'paid_amount')) {
                $table->dropColumn('paid_amount');
            }
        });
    }
};
