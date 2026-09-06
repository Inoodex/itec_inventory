<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productIdsInInventory = Inventory::pluck('product_id')->toArray();
        $products = Product::whereNotIn('id', $productIdsInInventory)
                        ->latest()
                        ->get();
        $inventories = Inventory::with('product')->latest()->get();
        return view('frontend.pages.inventory.index', compact('products','inventories'));
    }

    public function downloadPdf()
    {
        ini_set('memory_limit', '512M');
        $inventories = Inventory::with(['product.brand', 'product.category'])->latest()->get();
        $html = view('pdf.inventory', compact('inventories'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        $fileName = 'Inventory_Stock_Report_' . now()->format('Y_m_d_His') . '.pdf';
        $pdfContent = $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
       $request->validate([
            'product_id' => 'required|exists:products,id',
            'opening_stock' => 'required|integer|min:0',
        ]);

        $inventory = new Inventory();
        $inventory->product_id = $request->product_id;
        $inventory->opening_stock = $request->opening_stock;
        $inventory->current_stock = $request->opening_stock;
        $inventory->notes = 'Opening stock entry';
        $inventory->save();

        return redirect()->back()->with('success', 'Product with opening stock added successfully.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!auth()->user()->hasRole(['Super Admin', 'Admin', 'admin'])) {
            abort(403, 'Unauthorized action. Only Admin or Super Admin can edit inventory stock.');
        }

        $request->validate([
            'opening_stock' => 'required|integer|min:0',
            'current_stock' => 'required|integer|min:0',
            'notes'         => 'nullable|string|max:500',
        ]);

        $inventory = Inventory::findOrFail($id);
        $inventory->update([
            'opening_stock' => $request->opening_stock,
            'current_stock' => $request->current_stock,
            'notes'         => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Inventory stock updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->hasRole(['Super Admin', 'Admin', 'admin'])) {
            abort(403, 'Unauthorized action. Only Admin or Super Admin can delete inventory records.');
        }

        $inventory = Inventory::findOrFail($id);
        $inventory->delete();

        return redirect()->back()->with('success', 'Inventory record deleted successfully.');
    }
}
