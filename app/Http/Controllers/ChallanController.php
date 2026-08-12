<?php

namespace App\Http\Controllers;

use App\Models\Challan;
use App\Models\ChallanItem;
use App\Models\CompanyDetail;
use App\Models\Sale;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ChallanController extends Controller
{

public function index(Request $request)
{

    $query = Challan::with('challanItems');
    
    // Type filter
    if ($request->has('type') && $request->type != '') {
        $query->where('type', $request->type);
    }
    
    // Date range filter
    if ($request->has('date_from') && $request->date_from != '') {
        $query->whereDate('challan_date', '>=', $request->date_from);
    }
    
    if ($request->has('date_to') && $request->date_to != '') {
        $query->whereDate('challan_date', '<=', $request->date_to);
    }
    
    $challans = $query->latest()->paginate(10);

    
    return view('frontend.pages.challans.index', compact('challans'));
}

    public function create()
    {
        $companyDetails = CompanyDetail::where('is_active', true)->get();
        return view('frontend.pages.challans.create', compact('companyDetails'));
    }

    public function store(Request $request)
    {
        \Log::info('Challan store method called', $request->all());

        try {
            $validated = $request->validate([
                'type' => 'required|in:sale,project',
                'reference_number' => 'required',
                'challan_date' => 'required|date',
                'selected_sale_id' => 'required_if:type,sale',
                'selected_project_id' => 'required_if:type,project',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit' => 'required|string',
            ]);

            // Generate challan number
            $challanNumber = 'CHALLAN-' . date('Ymd') . '-' . str_pad(Challan::count() + 1, 4, '0', STR_PAD_LEFT);

            // Set customer_id and client_id based on type
            $customerId = null;
            $clientId = null;

            if ($request->type === 'sale' && $request->selected_sale_id) {
                $sale = Sale::find($request->selected_sale_id);
                $customerId = $sale->customer_id ?? null;
            } elseif ($request->type === 'project' && $request->selected_project_id) {
                $project = Project::find($request->selected_project_id);
                $clientId = $project->client_id ?? null;
            }

            // Create the challan with all details
            $challan = Challan::create([
                'challan_number' => $challanNumber,
                'reference_number' => $validated['reference_number'],
                'challan_date' => $validated['challan_date'],
                'type' => $validated['type'],
                'sale_id' => $request->type === 'sale' ? $validated['selected_sale_id'] : null,
                'project_id' => $request->type === 'project' ? $validated['selected_project_id'] : null,
                'customer_id' => $customerId,
                'client_id' => $clientId,
                'recipient_organization' => $request->recipient_organization,
                'recipient_designation' => $request->recipient_designation ?? 'The Managing Director',
                'recipient_address' => $request->recipient_address,
                'attention_to' => $request->attention_to,
                'designation' => $request->recipient_designation,
                'subject' => $request->subject ?? 'Delivery Challan',
                'notes' => $request->notes,
                'company_name' => $request->company_name ?? 'Intelligent Technology',
                'signatory_name' => $request->signatory_name ?? 'Engr. Shamsul Alam',
                'signatory_designation' => $request->signatory_designation ?? 'Director (Technical)',
                'company_phone' => $request->company_phone ?? '+880 XXXX-XXXXXX',
                'company_email' => $request->company_email ?? 'info@intelligenttech.com',
                'company_website' => $request->company_website ?? 'www.itechbd.net',
                'show_signature' => $request->has('show_signature') ? (bool)$request->show_signature : true,
                'show_seal' => $request->has('show_seal') ? (bool)$request->show_seal : true,
            ]);

            // Add challan items
            foreach ($validated['items'] as $item) {
                ChallanItem::create([
                    'challan_id' => $challan->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'serial' => $item['serial'] ?? null,
                ]);
            }

            // Load challan with relationships for PDF
            $challan->load('challanItems', 'sale.customer', 'project.client');

            // Look up signature image from CompanyDetail
            $signatoryName = $challan->signatory_name ?? 'Engr. Shamsul Alam';
            $companyDetail = CompanyDetail::where('signatory_name', $signatoryName)->first();

            $pdfData = [
                'challan' => $challan,
                'recipient_organization' => $challan->recipient_organization ?? 'N/A',
                'recipient_designation' => $challan->recipient_designation ?? 'The Managing Director',
                'recipient_address' => $challan->recipient_address ?? 'N/A',
                'attention_to' => $challan->attention_to ?? '',
                'subject' => $challan->subject ?? 'Delivery Challan',
                'show_signature' => $challan->show_signature ?? true,
                'show_seal' => $challan->show_seal ?? true,
                'signature_image' => $companyDetail->signature_image ?? null,
                'seal_image' => $companyDetail->seal_image ?? null,
            ];

    $html = view('pdf.challan', $pdfData)->render();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'Helvetica',
    ]);
    $mpdf->WriteHTML($html);
    $fileRecipientName = $challan->sale?->customer?->name
        ?? $challan->project?->client?->name
        ?? $request->recipient_organization
        ?? 'client';
    $recipientSlug = Str::slug($fileRecipientName);
    $challanDate = $challan->challan_date
        ? Carbon::parse($challan->challan_date)->format('d-m-Y')
        : now()->format('d-m-Y');
    $fileName = $recipientSlug . '-' . $challanDate . '.pdf';

    return response($mpdf->Output($fileName, 'I'), 200, [
        'Content-Type' => 'application/pdf',
    ]);

        } catch (\Exception $e) {
            \Log::error('Challan creation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Error creating challan: ' . $e->getMessage())->withInput();
        }
    }
    public function show($id)
    {
        $challan = Challan::with(['challanItems', 'sale.customer', 'project.client'])->findOrFail($id);
        return view('challans.show', compact('challan'));
    }

public function preview($id)
{
    $challan = Challan::with([
        'challanItems',
        'sale.customer',
        'project.client'
    ])->findOrFail($id);

    $recipientName = $challan->recipient_organization;
    $recipientAddress = $challan->recipient_address;
    
    if (!$recipientName) {
        if ($challan->type === 'sale' && $challan->sale && $challan->sale->customer) {
            $recipientName = $challan->sale->customer->name;
        } elseif ($challan->type === 'project' && $challan->project && $challan->project->client) {
            $recipientName = $challan->project->client->name;
        }
    }
    
    if (!$recipientAddress) {
        if ($challan->type === 'sale' && $challan->sale && $challan->sale->customer) {
            $recipientAddress = $challan->sale->customer->address;
        } elseif ($challan->type === 'project' && $challan->project && $challan->project->client) {
            $recipientAddress = $challan->project->client->address;
        }
    }

    // Look up signature image from CompanyDetail
    $signatoryName = $challan->signatory_name ?? 'Engr. Shamsul Alam';
    $companyDetail = CompanyDetail::where('signatory_name', $signatoryName)->first();

    $pdfData = [
        'challan' => $challan,
        'recipient_organization' => $recipientName ?? 'N/A',
        'recipient_designation' => $challan->recipient_designation ?? 'The Managing Director',
        'recipient_address' => $recipientAddress ?? 'N/A',
        'attention_to' => $challan->attention_to ?? '',
        'subject' => $challan->subject ?? 'Delivery Challan',
        'show_signature' => $challan->show_signature ?? true,
        'show_seal' => $challan->show_seal ?? true,
        'signature_image' => $companyDetail->signature_image ?? null,
        'seal_image' => $companyDetail->seal_image ?? null,
    ];

    $html = view('pdf.challan', $pdfData)->render();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'Helvetica',
    ]);
    $mpdf->WriteHTML($html);
    return response($mpdf->Output('challan-' . $challan->id . '.pdf', 'I'), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}

public function download($id)
{
    $challan = Challan::with([
        'challanItems',
        'sale.customer',
        'project.client'
    ])->findOrFail($id);

    // Use data from database first, fallback to relationships if needed
    $recipientName = $challan->recipient_organization;
    $recipientAddress = $challan->recipient_address;
    
    // If recipient_organization is not stored, try to get from relationships
    if (!$recipientName) {
        if ($challan->type === 'sale' && $challan->sale && $challan->sale->customer) {
            $recipientName = $challan->sale->customer->name;
        } elseif ($challan->type === 'project' && $challan->project && $challan->project->client) {
            $recipientName = $challan->project->client->name;
        }
    }
    
    // If recipient_address is not stored, try to get from relationships
    if (!$recipientAddress) {
        if ($challan->type === 'sale' && $challan->sale && $challan->sale->customer) {
            $recipientAddress = $challan->sale->customer->address;
        } elseif ($challan->type === 'project' && $challan->project && $challan->project->client) {
            $recipientAddress = $challan->project->client->address;
        }
    }

    // Look up signature image from CompanyDetail
    $signatoryName = $challan->signatory_name ?? 'Engr. Shamsul Alam';
    $companyDetail = CompanyDetail::where('signatory_name', $signatoryName)->first();

    $pdfData = [
        'challan' => $challan,
        'recipient_organization' => $recipientName ?? 'N/A',
        'recipient_designation' => $challan->recipient_designation ?? 'The Managing Director',
        'recipient_address' => $recipientAddress ?? 'N/A',
        'attention_to' => $challan->attention_to ?? '',
        'subject' => $challan->subject ?? 'Delivery Challan',
        'show_signature' => $challan->show_signature ?? true,
        'show_seal' => $challan->show_seal ?? true,
        'signature_image' => $companyDetail->signature_image ?? null,
        'seal_image' => $companyDetail->seal_image ?? null,
    ];

    $html = view('pdf.challan', $pdfData)->render();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'Helvetica',
    ]);
    $mpdf->WriteHTML($html);
    $fileRecipientName = $recipientName ?? 'client';
    $recipientSlug = Str::slug($fileRecipientName);
    $challanDate = $challan->challan_date
        ? Carbon::parse($challan->challan_date)->format('d-m-Y')
        : now()->format('d-m-Y');
    $fileName = $recipientSlug . '-' . $challanDate . '.pdf';

    return response($mpdf->Output($fileName, 'I'), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}
    public function getSales()
    {
        try {
            $sales = Sale::with(['customer', 'client', 'items.product'])
                ->latest()
                ->get()
                ->map(function ($sale) {
                    $customerName = $sale->sale_type == 'project' ? ($sale->client->name ?? 'N/A') : ($sale->customer->name ?? 'N/A');
                    $customerPhone = $sale->sale_type == 'project' ? ($sale->client->phone ?? 'N/A') : ($sale->customer->phone ?? 'N/A');
                    $customerAddress = $sale->sale_type == 'project' ? ($sale->client->address ?? 'N/A') : ($sale->customer->address ?? 'N/A');

                    $items = $sale->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'description' => ($item->product->name ?? 'Product') . ($item->product && $item->product->model ? ' (' . $item->product->model . ')' : ''),
                            'quantity' => $item->qty ?? 1,
                            'unit' => 'Pcs',
                            'unit_price' => $item->unit_price ?? 0,
                            'total' => $item->total_price ?? 0,
                        ];
                    });

                    return [
                        'id' => $sale->id,
                        'order_no' => $sale->order_no,
                        'sale_type' => $sale->sale_type,
                        'date' => $sale->created_at ? $sale->created_at->format('Y-m-d') : '',
                        'created_at' => $sale->created_at ? $sale->created_at->format('d M Y') : '',
                        'customer_name' => $customerName,
                        'customer_phone' => $customerPhone,
                        'customer_address' => $customerAddress,
                        'payble' => $sale->payble ?? $sale->total ?? 0,
                        'total_amount' => $sale->payble ?? $sale->total ?? 0,
                        'due_payment' => $sale->due_payment ?? 0,
                        'customer' => [
                            'id' => $sale->customer_id ?? $sale->client_id,
                            'name' => $customerName,
                            'phone' => $customerPhone,
                            'address' => $customerAddress,
                        ],
                        'items' => $items
                    ];
                });

            return response()->json($sales);
        } catch (\Exception $e) {
            \Log::error('Error in getSales: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProjects()
    {
        try {
            $projects = Project::with(['client', 'projectItems.product'])
                ->latest()
                ->get()
                ->map(function ($project) {
                    $clientName = $project->client->name ?? 'N/A';
                    $clientAddress = $project->client->address ?? 'N/A';

                    return [
                        'id' => $project->id,
                        'name' => $project->name ?? $project->project_name ?? 'Project #' . $project->id,
                        'reference' => 'PROJ-' . $project->id,
                        'date' => $project->start_date ?? ($project->created_at ? $project->created_at->format('Y-m-d') : ''),
                        'created_at' => $project->created_at ? $project->created_at->format('d M Y') : '',
                        'client_name' => $clientName,
                        'client_address' => $clientAddress,
                        'budget' => $project->budget ?? 0,
                        'total_amount' => $project->budget ?? 0,
                        'due_payment' => $project->due_payment ?? 0,
                        'client' => [
                            'id' => $project->client->id ?? null,
                            'name' => $clientName,
                            'address' => $clientAddress,
                        ],
                        'items' => $project->projectItems->map(function ($item) {
                            $productName = $item->product ? $item->product->name : null;
                            return [
                                'id' => $item->id,
                                'description' => $item->description ?? $productName ?? 'Project Item',
                                'quantity' => $item->quantity ?? 1,
                                'unit' => $item->unit ?? 'Unit',
                                'unit_price' => $item->unit_price ?? 0,
                                'total' => $item->total ?? ($item->quantity * $item->unit_price),
                            ];
                        })
                    ];
                });

            return response()->json($projects);
        } catch (\Exception $e) {
            \Log::error('getProjects Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

public function reportPdf(Request $request)
{
    $query = Challan::with('challanItems');

    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('challan_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('challan_date', '<=', $request->date_to);
    }

    $challans = $query->latest()->get();

    $html = view('pdf.challans-report', compact('challans', 'request'))->render();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'Helvetica',
    ]);
    $mpdf->WriteHTML($html);
    return response($mpdf->Output('challans-report.pdf', 'I'), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}

public function destroy($id)
{
    try {
        $challan = Challan::findOrFail($id);
        $challan->delete();

        return redirect()->route('challans.index')->with('success', 'Challan deleted successfully.');
    } catch (\Exception $e) {
        \Log::error('Error deleting challan: ' . $e->getMessage());
        return redirect()->route('challans.index')->with('error', 'Failed to delete challan.');
    }
}
}
