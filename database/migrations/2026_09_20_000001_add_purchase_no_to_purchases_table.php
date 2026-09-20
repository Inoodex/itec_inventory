 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('purchase_no', 50)->nullable()->after('id')->index();
        });

        // Backfill existing purchases with legacy identifier PUR-0000X
        DB::table('purchases')->whereNull('purchase_no')->chunkById(200, function ($purchases) {
            foreach ($purchases as $purchase) {
                DB::table('purchases')
                    ->where('id', $purchase->id)
                    ->update([
                        'purchase_no' => 'PUR-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT)
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('purchase_no');
        });
    }
};
