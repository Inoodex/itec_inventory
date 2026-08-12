<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectBill;
use App\Models\BillItem;
use Illuminate\Http\Request;

class ProjectBillController extends Controller
{
    public function create(Project $project)
    {
        return view('frontend.pages.projects.bills.create', compact('project'));
    }

public function storeBill(Request $request, Project $project)
{
    $validated = $request->validate([
        'reference_number' => 'required|string|unique:project_bills,reference_number',
        'work_order_number' => 'required|string',
        'recipient_name' => 'required|string',
        'recipient_designation' => 'required|string',
        'recipient_organization' => 'required|string',
        'recipient_address' => 'required|string',
        'attention_to' => 'nullable|string',
        'subject' => 'required|string',
        'bill_date' => 'required|date',
        'due_date' => 'required|date',
        'items' => 'required|array',
        'items.*.description' => 'required|string',
        'items.*.quantity' => 'required|numeric|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.item_id' => 'nullable|exists:project_items,id',
        'items.*.product_id' => 'nullable|exists:products,id',
        'total_amount' => 'required|numeric|min:0',
        'amount_in_words' => 'required|string',
        'terms' => 'required|array',
        'terms.*' => 'required|string',
        'bank_details' => 'required|array',
        'bank_details.account_name' => 'required|string',
        'bank_details.bank_name' => 'required|string',
        'bank_details.branch' => 'required|string',
        'bank_details.account_number' => 'required|string',
        'bank_details.account_type' => 'required|string',
        'bank_details.routing_number' => 'nullable|string',
        'bank_details.mobile' => 'nullable|string',
    ]);

    try {
        \DB::beginTransaction();

        // Calculate totals from items
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        // Generate unique bill number
        $billNumber = 'BIL-' . now()->format('Ymd-His') . '-' . str_pad(ProjectBill::count() + 1, 3, '0', STR_PAD_LEFT);

        // Create the bill - NO TAX FIELD
        $bill = ProjectBill::create([
            'project_id' => $project->id,
            'bill_number' => $billNumber,
            'reference_number' => $validated['reference_number'],
            'work_order_number' => $validated['work_order_number'],
            'recipient_name' => $validated['recipient_name'],
            'recipient_designation' => $validated['recipient_designation'],
            'recipient_organization' => $validated['recipient_organization'],
            'recipient_address' => $validated['recipient_address'],
            'attention_to' => $validated['attention_to'] ?? null,
            'bill_date' => $validated['bill_date'],
            'due_date' => $validated['due_date'],
            'subtotal' => $subtotal,
            'tax_amount' => 0, // Set to 0 since we removed tax field
            'total_amount' => $validated['total_amount'],
            'notes' => $validated['subject'],
            'amount_in_words' => $validated['amount_in_words'],
            'terms_conditions' => implode("\n", $validated['terms']),
            'status' => 'draft',
            'bank_account_name' => $validated['bank_details']['account_name'],
            'bank_name' => $validated['bank_details']['bank_name'],
            'bank_branch' => $validated['bank_details']['branch'],
            'bank_account_number' => $validated['bank_details']['account_number'],
            'bank_account_type' => $validated['bank_details']['account_type'],
            'bank_routing_number' => $validated['bank_details']['routing_number'] ?? null,
            'bank_mobile' => $validated['bank_details']['mobile'] ?? null,
        ]);

        // Create bill items
        foreach ($validated['items'] as $itemData) {
            BillItem::create([
                'project_bill_id' => $bill->id,
                'project_item_id' => $itemData['item_id'] ?? null,
                'product_id' => $itemData['product_id'] ?? null,
                'description' => $itemData['description'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'total' => $itemData['quantity'] * $itemData['unit_price'],
                'unit' => $itemData['unit'] ?? 'No',
            ]);
        }

        \DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Bill generated successfully!',
            'bill' => $bill,
            'download_url' => route('projects.bills.download', $bill),
            'preview_url' => route('projects.bills.preview', $bill),
        ]);

    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Bill Generation Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error generating bill: ' . $e->getMessage(),
        ], 500);
    }
}
    public function download(ProjectBill $bill)
    {
        $fileName = "bill-{$bill->reference_number}.pdf";
        $pdf = $bill->generatePdf();
        
        return $pdf->download($fileName);
    }

    public function preview(ProjectBill $bill)
    {
        $pdf = $bill->generatePdf();
        return $pdf->stream("bill-{$bill->reference_number}.pdf");
    }
}