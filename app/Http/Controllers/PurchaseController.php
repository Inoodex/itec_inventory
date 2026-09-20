<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Inventory;
use App\Models\ProductSerial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePurchaseRequest;
use App\Services\PurchaseService;
use Carbon\Carbon;

class PurchaseController extends Controller
{
    public function __construct(private PurchaseService $purchaseService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Purchase::query();

        // Filter by search term (invoice no, product, vendor)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purchase_no', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")->orWhere('model', 'like', "%{$search}%");
                  })->orWhereHas('vendor', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by vendor
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by date range
        if ($request->filled('from') && $request->filled('to')) {
            $from = date('Y-m-d 00:00:00', strtotime($request->from));
            $to = date('Y-m-d 23:59:59', strtotime($request->to));
            $query->whereBetween('created_at', [$from, $to]);
        }

        // Grouping purchases by purchase_no
        $paginatedInvoices = (clone $query)
            ->select('purchase_no')
            ->selectRaw('MAX(id) as latest_id')
            ->groupBy('purchase_no')
            ->orderByDesc('latest_id')
            ->paginate(10)
            ->withQueryString();

        $invoiceNos = $paginatedInvoices->pluck('purchase_no')->filter()->toArray();

        $itemsByInvoice = Purchase::with(['product.brand', 'vendor', 'creator', 'serials'])
            ->whereIn('purchase_no', $invoiceNos)
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('purchase_no');

        // Total stats across all purchases
        $allPurchases = Purchase::all();
        $totalOrdersCount = $allPurchases->pluck('purchase_no')->unique()->count();
        $totalAmountSum = (float) $allPurchases->sum('total_price');
        $totalPaidSum = (float) $allPurchases->sum('payment');
        $totalDueSum = (float) $allPurchases->sum('due');

        $products = Product::latest()->get();
        $vendors = Vendor::latest()->get();

        return view('frontend.pages.purchase.index', compact(
            'paginatedInvoices',
            'itemsByInvoice',
            'totalOrdersCount',
            'totalAmountSum',
            'totalPaidSum',
            'totalDueSum',
            'products',
            'vendors'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('latestPurchase')->latest()->get();
        $vendors = Vendor::latest()->get();
        $paymentAccounts = getPaymentAccounts();
        $paymentMethods = getPaymentMethodList();

        return view('frontend.pages.purchase.create', compact('products', 'vendors', 'paymentAccounts', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage (single item modal/API fallback).
     */
    public function store(StorePurchaseRequest $request)
    {
        try {
            $this->purchaseService->createPurchase($request->validated());

            return redirect()->back()
                ->with('success', 'Purchase created and inventory updated successfully.');

        } catch (\RuntimeException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An unexpected error occurred. Please try again.')
                ->withInput();
        }
    }

    /**
     * Store batch/multi-item purchase from the dedicated create page.
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.serial_numbers' => 'nullable|array',
            'items.*.serial_numbers.*' => 'string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'payment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'payment_ref' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $vendorId = $validated['vendor_id'];
            $items = $validated['items'];
            $totalPayment = (float)($request->payment ?? 0);
            $totalDiscount = (float)($request->discount ?? 0);
            $paymentMethod = $validated['payment_method'] ?? 'cash';
            $accountId = $validated['account_id'] ?? null;
            $paymentRef = $validated['payment_ref'] ?? null;

            // Generate a shared purchase invoice number for this batch
            $purchaseNo = Purchase::generatePurchaseNo();

            // Compute total gross amount
            $grossTotal = 0;
            foreach ($items as $item) {
                $grossTotal += ((float)$item['unit_price'] * (int)$item['quantity']);
            }

            $netTotal = max(0, $grossTotal - $totalDiscount);
            $remainingPayment = min($totalPayment, $netTotal);

            // Process each item purchase
            foreach ($items as $item) {
                $itemQty = (int)$item['quantity'];
                $unitPrice = (float)$item['unit_price'];
                $itemGross = $unitPrice * $itemQty;

                // Allocate discount proportionally if any
                $itemDiscount = $grossTotal > 0 ? ($itemGross / $grossTotal) * $totalDiscount : 0;
                $itemNet = max(0, $itemGross - $itemDiscount);

                // Allocate payment
                $itemPayment = min($remainingPayment, $itemNet);
                $remainingPayment -= $itemPayment;
                $itemDue = max(0, $itemNet - $itemPayment);

                $purchaseData = [
                    'purchase_no' => $purchaseNo,
                    'product_id' => $item['product_id'],
                    'vendor_id' => $vendorId,
                    'quantity' => $itemQty,
                    'unit_price' => $unitPrice,
                    'sub_price' => $itemGross,
                    'total_price' => $itemNet,
                    'payment' => $itemPayment,
                    'due' => $itemDue,
                    'serial_numbers' => $item['serial_numbers'] ?? [],
                    'payment_method' => $paymentMethod,
                    'account_id' => $accountId,
                    'payment_ref' => $paymentRef,
                ];

                $this->purchaseService->createPurchase($purchaseData);
            }

            DB::commit();

            return redirect()->route('purchase.index')
                ->with('success', 'Purchases created and inventory updated successfully.');

        } catch (\RuntimeException $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Batch purchase error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchase = Purchase::with(['product.brand', 'vendor', 'creator', 'serials'])->findOrFail($id);

        if (!empty($purchase->purchase_no)) {
            $items = Purchase::with(['product.brand', 'vendor', 'creator', 'serials'])
                ->where('purchase_no', $purchase->purchase_no)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $items = collect([$purchase]);
        }

        $products = Product::with('latestPurchase')->latest()->get();
        $vendors = Vendor::latest()->get();
        $paymentAccounts = getPaymentAccounts();
        $paymentMethods = getPaymentMethodList();

        $purchaseNo = $purchase->purchase_no ?? ('PUR-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT));
        $vendorId = $purchase->vendor_id;
        $purchaseDate = $purchase->created_at ? $purchase->created_at->format('Y-m-d') : date('Y-m-d');

        $subTotal = (float) $items->sum(fn($i) => $i->sub_price ?? ($i->unit_price * $i->quantity));
        $totalAmount = (float) $items->sum('total_price');
        $discount = max(0, $subTotal - $totalAmount);
        $payment = (float) $items->sum('payment');
        $due = (float) $items->sum('due');

        // Prepare initial cart array for the frontend builder
        $cartItems = $items->map(function ($item) {
            return [
                'product_id' => (int) $item->product_id,
                'product_name' => ($item->product->name ?? 'Product') . ($item->product->model ? ' (' . $item->product->model . ')' : ''),
                'unit_price' => (float) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'total_price' => (float) $item->total_price,
                'is_serialized' => (bool) ($item->product->is_serialized ?? false),
                'serial_numbers' => $item->serials ? $item->serials->pluck('serial_number')->toArray() : [],
            ];
        })->values();

        return view('frontend.pages.purchase.edit', compact(
            'purchase',
            'items',
            'products',
            'vendors',
            'paymentAccounts',
            'paymentMethods',
            'purchaseNo',
            'vendorId',
            'purchaseDate',
            'subTotal',
            'totalAmount',
            'discount',
            'payment',
            'due',
            'cartItems'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'purchase_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.serial_numbers' => 'nullable|array',
            'items.*.serial_numbers.*' => 'string|max:100',
            'discount' => 'nullable|numeric|min:0',
            'payment' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string|max:50',
            'account_id' => 'nullable|exists:chart_of_accounts,id',
            'payment_ref' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();

            $vendorId = $validated['vendor_id'];
            $items = $validated['items'];
            $totalPayment = (float)($request->payment ?? 0);
            $totalDiscount = (float)($request->discount ?? 0);
            $paymentMethod = $validated['payment_method'] ?? 'cash';
            $accountId = $validated['account_id'] ?? null;
            $paymentRef = $validated['payment_ref'] ?? null;
            $purchaseNo = $purchase->purchase_no ?? ('PUR-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT));

            // 1. Fetch old items belonging to this purchase batch
            $oldItems = Purchase::where('purchase_no', $purchaseNo)->get();
            if ($oldItems->isEmpty()) {
                $oldItems = collect([$purchase]);
            }

            // 2. Revert previous inventory additions and serials for old items
            foreach ($oldItems as $oldItem) {
                // Decrement inventory stock
                $inventory = Inventory::where('product_id', $oldItem->product_id)->first();
                if ($inventory) {
                    $inventory->current_stock = max(0, $inventory->current_stock - $oldItem->quantity);
                    $inventory->save();
                }
                // Remove old serial numbers linked to this purchase item
                ProductSerial::where('purchase_id', $oldItem->id)->delete();
            }

            // 3. Delete old purchase items from DB
            Purchase::whereIn('id', $oldItems->pluck('id'))->delete();

            // 4. Compute total gross amount
            $grossTotal = 0;
            foreach ($items as $item) {
                $grossTotal += ((float)$item['unit_price'] * (int)$item['quantity']);
            }

            $netTotal = max(0, $grossTotal - $totalDiscount);
            $remainingPayment = min($totalPayment, $netTotal);

            // 5. Insert updated items under the same purchase_no
            foreach ($items as $item) {
                $itemQty = (int)$item['quantity'];
                $unitPrice = (float)$item['unit_price'];
                $itemGross = $unitPrice * $itemQty;

                // Allocate discount proportionally if any
                $itemDiscount = $grossTotal > 0 ? ($itemGross / $grossTotal) * $totalDiscount : 0;
                $itemNet = max(0, $itemGross - $itemDiscount);

                // Allocate payment
                $itemPayment = min($remainingPayment, $itemNet);
                $remainingPayment -= $itemPayment;
                $itemDue = max(0, $itemNet - $itemPayment);

                $purchaseData = [
                    'purchase_no' => $purchaseNo,
                    'product_id' => $item['product_id'],
                    'vendor_id' => $vendorId,
                    'quantity' => $itemQty,
                    'unit_price' => $unitPrice,
                    'sub_price' => $itemGross,
                    'total_price' => $itemNet,
                    'payment' => $itemPayment,
                    'due' => $itemDue,
                    'serial_numbers' => $item['serial_numbers'] ?? [],
                    'payment_method' => $paymentMethod,
                    'account_id' => $accountId,
                    'payment_ref' => $paymentRef,
                ];

                $this->purchaseService->createPurchase($purchaseData);
            }

            DB::commit();

            return redirect()->route('purchase.index')
                ->with('success', "Purchase order #{$purchaseNo} updated successfully.");

        } catch (\RuntimeException $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Update purchase error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An unexpected error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        if (!empty($purchase->purchase_no)) {
            Purchase::where('purchase_no', $purchase->purchase_no)->delete();
        } else {
            $purchase->delete();
        }
        return redirect()->back()->with('success', 'Purchase order deleted successfully.');
    }

    public function getLatestPrice($id)
    {
        $product = Product::with('latestPurchase')->find($id);

        if (!$product) {
            return response()->json(['price' => 0]);
        }

        $price = $product->latestPurchase ? $product->latestPurchase->unit_price : 0;

        return response()->json(['price' => $price]);
    }

    public function reportIndex(Request $request)
    {
        return $this->report($request);
    }

    public function report(Request $request)
    {
        $query = Purchase::query();
        $query = $this->applyPurchaseReportFilters($query, $request);

        $purchases = $query
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_amount')
            ->groupBy('product_id')
            ->with('product')
            ->get();

        $products = Product::latest()->get();
        $vendors = Vendor::latest()->get();

        return view('frontend.pages.report.purchase.index', compact('purchases', 'products', 'vendors', 'request'));
    }

    public function reportPdf(Request $request)
    {
        $query = Purchase::query();
        $query = $this->applyPurchaseReportFilters($query, $request);

        $purchases = $query
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(total_price) as total_amount')
            ->groupBy('product_id')
            ->with('product')
            ->get();

        $products = Product::latest()->get();
        $vendors = Vendor::latest()->get();

        $filters = [
            'from' => $request->filled('from') ? $request->from : Carbon::now()->startOfMonth()->format('Y-m-d'),
            'to' => $request->filled('to') ? $request->to : Carbon::now()->endOfMonth()->format('Y-m-d'),
            'product' => $request->filled('item_name') ? Product::find($request->item_name)?->name : 'All Products',
            'vendor' => $request->filled('vendor_id') ? Vendor::find($request->vendor_id)?->name : 'All Vendors',
        ];

        ini_set('memory_limit', '512M');
        $html = view('frontend.pages.report.purchase.pdf', compact('purchases', 'filters'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        $fileName = 'purchase-report-' . now()->format('Y-m-d') . '.pdf';
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    private function applyPurchaseReportFilters($query, Request $request)
    {
        if ($request->filled('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        if ($request->filled('item_name')) {
            $query->where('product_id', $request->item_name);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return $query;
    }

    /**
     * Download or view consolidated purchase invoice PDF.
     */
    public function downloadInvoicePdf($id)
    {
        $purchase = Purchase::with(['product.brand', 'vendor', 'creator', 'serials'])->find($id);
        if (!$purchase) {
            abort(404, 'Purchase record not found.');
        }

        // Retrieve all items for this purchase invoice (or single item if no batch)
        if (!empty($purchase->purchase_no)) {
            $items = Purchase::with(['product.brand', 'vendor', 'creator', 'serials'])
                ->where('purchase_no', $purchase->purchase_no)
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $items = collect([$purchase]);
        }

        $product = $purchase->product;
        $vendor = $purchase->vendor;
        $creator = $purchase->creator;
        $purchaseNo = $purchase->purchase_no ?? ('PUR-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT));
        $createdAt = $purchase->created_at;

        // Calculate combined financial totals
        $subTotal = (float) $items->sum(function ($item) {
            return $item->sub_price ?? ($item->quantity * $item->unit_price);
        });
        $totalAmount = (float) $items->sum('total_price');
        $totalDiscount = max(0, $subTotal - $totalAmount);
        $totalPayment = (float) $items->sum('payment');
        $totalDue = (float) $items->sum('due');

        try {
            ini_set('memory_limit', '512M');

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_top' => 42,
                'margin_bottom' => 32,
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_footer' => 24,
                'default_font' => 'Helvetica',
            ]);

            $html = view('frontend.pages.purchase.invoice_pdf', compact(
                'purchase',
                'items',
                'product',
                'vendor',
                'creator',
                'purchaseNo',
                'createdAt',
                'subTotal',
                'totalDiscount',
                'totalAmount',
                'totalPayment',
                'totalDue'
            ))->render();
            $mpdf->WriteHTML($html);

            $filename = $purchaseNo . '.pdf';
            $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to generate PDF invoice: ' . $e->getMessage());
        }
    }
}
