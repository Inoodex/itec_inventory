<?php

namespace App\Http\Controllers;

use App\Models\ProductReturn;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Http\Requests\StoreReturnRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    /**
     * Display a listing of returns.
     */
    public function index(Request $request)
    {
        $query = ProductReturn::with(['sale', 'customer', 'items.product'])
            ->latest();

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('sale', function ($sq) use ($search) {
                        $sq->where('order_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            });
        }

        $returns = $query->get();
        $customers = Customer::all();

        return view('frontend.pages.returns.index', compact('returns', 'customers'));
    }

    /**
     * Show form to create a new return.
     */
    public function create(Request $request)
    {
        $sale = null;
        $saleItems = [];

        if ($request->filled('sale_id')) {
            $sale = Sale::with(['customer', 'items.product'])->find($request->sale_id);
            if ($sale) {
                $saleItems = $sale->items;
            }
        }

        $sales = Sale::with('customer')
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();

        $customers = Customer::all();
        $products = Product::all();

        return view('frontend.pages.returns.create', compact('sale', 'saleItems', 'sales', 'customers', 'products'));
    }

    /**
     * Store a newly created return.
     */
    public function store(StoreReturnRequest $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'return_date' => 'required|date',
            'reason' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.return_reason' => 'required|in:damaged,wrong_item,customer_changed_mind,defective,expired,other',
            'items.*.condition' => 'required|in:good,damaged,defective',
            'items.*.notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::find($request->sale_id);

            // Create return
            $return = ProductReturn::create([
                'sale_id' => $request->sale_id,
                'customer_id' => $sale->customer_id,
                'return_date' => $request->return_date,
                'status' => 'pending',
                'reason' => $request->reason,
                'total_refund_amount' => 0
            ]);

            // Create return items
            foreach ($request->items as $item) {
                $item['total_price'] = $item['quantity'] * $item['unit_price'];
                $item['return_id'] = $return->id;
                ReturnItem::create($item);
            }

            // Update total refund amount
            $return->total_refund_amount = $return->calculateTotalRefund();
            $return->save();

            DB::commit();

            return redirect()->route('returns.index')
                ->with('success', 'Return request created successfully. Status: Pending');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating return: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified return.
     */
    public function show($id)
    {
        $return = ProductReturn::with(['sale', 'customer', 'items.product', 'processedBy'])->findOrFail($id);
        return view('frontend.pages.returns.show', compact('return'));
    }

    /**
     * Approve the return.
     */
    public function approve($id)
    {
        $return = ProductReturn::findOrFail($id);

        if (!$return->isPending()) {
            return back()->with('error', 'Return is not in pending status');
        }

        $return->approve(auth()->id());

        return back()->with('success', 'Return approved successfully');
    }

    /**
     * Complete the return and add items back to stock.
     */
    public function complete($id)
    {
        $return = ProductReturn::with('items')->findOrFail($id);

        if (!$return->isApproved()) {
            return back()->with('error', 'Return must be approved before completing');
        }

        DB::beginTransaction();

        try {
            $return->complete(auth()->id());
            DB::commit();

            return back()->with('success', 'Return completed successfully. Stock updated.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error completing return: ' . $e->getMessage());
        }
    }

    /**
     * Reject the return.
     */
    public function reject(Request $request, $id)
    {
        $return = ProductReturn::findOrFail($id);

        if (!$return->isPending()) {
            return back()->with('error', 'Return is not in pending status');
        }

        $return->reject(auth()->id(), $request->reason);

        return back()->with('success', 'Return rejected');
    }

    /**
     * Get sale items for return (AJAX).
     */
    public function getSaleItems($saleId)
    {
        $sale = Sale::with(['items.product', 'customer'])->find($saleId);

        if (!$sale) {
            return response()->json(['error' => 'Sale not found'], 404);
        }

        return response()->json([
            'sale' => $sale,
            'items' => $sale->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name ?? 'N/A',
                    'quantity' => $item->qty,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price
                ];
            })
        ]);
    }

    /**
     * Remove the specified return.
     */
    public function destroy($id)
    {
        $return = ProductReturn::findOrFail($id);

        if (!$return->isPending() && !$return->isRejected()) {
            return back()->with('error', 'Only pending or rejected returns can be deleted');
        }

        DB::beginTransaction();

        try {
            // Delete return items first
            $return->items()->delete();
            // Delete return
            $return->delete();

            DB::commit();

            return redirect()->route('returns.index')
                ->with('success', 'Return deleted successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error deleting return: ' . $e->getMessage());
        }
    }
}
