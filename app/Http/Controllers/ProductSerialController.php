<?php

namespace App\Http\Controllers;

use App\Models\ProductSerial;
use Illuminate\Http\Request;

class ProductSerialController extends Controller
{
    /**
     * Get available serials for a specific product as JSON.
     */
    public function getSerialsByProduct($productId)
    {
        $serials = ProductSerial::with('purchase.vendor')
            ->where('product_id', $productId)
            ->where('status', 'available')
            ->latest()
            ->get();

        return response()->json([
            'serials' => $serials
        ]);
    }
}
