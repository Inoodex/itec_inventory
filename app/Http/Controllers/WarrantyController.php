<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWarrantyClaimRequest;
use App\Http\Requests\UpdateWarrantyClaimRequest;
use App\Models\WarrantyClaim;
use App\Services\WarrantyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WarrantyController extends Controller
{
    public function __construct(private WarrantyService $warrantyService) {}

    public function index(Request $request)
    {
        $query = WarrantyClaim::with(['sale', 'product', 'customer', 'receiver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('claim_no', 'LIKE', "%{$search}%")
                  ->orWhere('serial_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'LIKE', "%{$search}%")
                         ->orWhere('phone', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('claim_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('claim_date', '<=', $request->date_to);
        }

        $claims = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total'            => WarrantyClaim::count(),
            'pending'          => WarrantyClaim::where('status', 'pending')->count(),
            'under_inspection' => WarrantyClaim::where('status', 'under_inspection')->count(),
            'sent_to_vendor'   => WarrantyClaim::where('status', 'sent_to_vendor')->count(),
            'repaired'         => WarrantyClaim::where('status', 'repaired')->count(),
            'replaced'         => WarrantyClaim::where('status', 'replaced')->count(),
            'completed'        => WarrantyClaim::where('status', 'completed')->count(),
        ];

        return view('frontend.pages.warranties.index', compact('claims', 'stats', 'request'));
    }

    public function create(Request $request)
    {
        $searchResults = collect();

        if ($request->filled('search')) {
            $searchResults = $this->warrantyService->searchEligibleSaleItems($request->search);
        }

        return view('frontend.pages.warranties.create', compact('searchResults', 'request'));
    }

    public function store(StoreWarrantyClaimRequest $request)
    {
        try {
            $claim = $this->warrantyService->createClaim($request->validated());

            return redirect()->route('warranties.show', $claim->id)
                ->with('success', "Warranty Claim {$claim->claim_no} registered successfully.");
        } catch (\Exception $e) {
            Log::error("Warranty Claim creation failed: " . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to register warranty claim: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $claim = WarrantyClaim::with(['sale.customer', 'salesItem', 'product', 'customer', 'receiver', 'logs.user'])
            ->findOrFail($id);

        return view('frontend.pages.warranties.show', compact('claim'));
    }

    public function update(UpdateWarrantyClaimRequest $request, $id)
    {
        try {
            $claim = WarrantyClaim::findOrFail($id);
            $this->warrantyService->updateClaimStatus($claim, $request->validated());

            return redirect()->route('warranties.show', $id)
                ->with('success', 'Warranty claim status updated successfully.');
        } catch (\Exception $e) {
            Log::error("Warranty Claim update failed: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update warranty claim: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $claim = WarrantyClaim::findOrFail($id);
            $claim->delete();

            return redirect()->route('warranties.index')
                ->with('success', 'Warranty claim deleted successfully.');
        } catch (\Exception $e) {
            Log::error("Warranty Claim deletion failed: " . $e->getMessage());
            return redirect()->route('warranties.index')
                ->with('error', 'Failed to delete warranty claim.');
        }
    }

    public function lookup(Request $request)
    {
        if (!$request->filled('search')) {
            return response()->json(['items' => []]);
        }

        $items = $this->warrantyService->searchEligibleSaleItems($request->search);

        return response()->json(['items' => $items]);
    }

    public function printReceipt($id)
    {
        $claim = WarrantyClaim::with(['sale.customer', 'salesItem', 'product', 'customer', 'receiver'])
            ->findOrFail($id);

        $html = view('pdf.warranty-receipt', compact('claim'))->render();
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'Helvetica',
        ]);
        $mpdf->WriteHTML($html);
        return response($mpdf->Output("warranty-claim-{$claim->claim_no}.pdf", 'I'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
