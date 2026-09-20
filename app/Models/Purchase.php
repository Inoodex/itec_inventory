<?php

namespace App\Models;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\LogsActivity;

class Purchase extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    // Optional: define table name if not following conventions
    protected $table = 'purchases';

    // Mass assignable fields
    protected $fillable = [
        'purchase_no',
        'product_id',
        'vendor_id',
        'quantity',
        'unit_price',
        'sub_price',
        'total_price',
        'payment',
        'due',
        'created_by',
        'updated_by',
    ];

    /**
     * Generate unique sequential purchase invoice number in format PUR-00054.
     */
    public static function generatePurchaseNo(): string
    {
        $allPurchaseNos = self::withTrashed()
            ->where('purchase_no', 'LIKE', 'PUR-%')
            ->pluck('purchase_no');

        $maxSeq = 0;
        foreach ($allPurchaseNos as $no) {
            if (preg_match('/^PUR-(\d+)$/', (string)$no, $matches)) {
                $num = (int) $matches[1];
                if ($num > $maxSeq) {
                    $maxSeq = $num;
                }
            }
        }

        $maxId = (int) self::withTrashed()->max('id');
        $nextSeq = max($maxSeq, $maxId) + 1;

        return 'PUR-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class, 'purchase_id');
    }
}
