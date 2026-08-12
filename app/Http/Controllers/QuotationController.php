<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Client;
use App\Models\CompanyDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuotationController extends Controller
{
public function index(Request $request)
{
    $query = Quotation::with('client');

    if ($request->filled('date_from')) {
        $query->whereDate('quotation_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('quotation_date', '<=', $request->date_to);
    }

    $quotations = $query->latest()->get();
    return view('frontend.pages.quotations.index', compact('quotations'));
}

    public function create()
    {
        $clients = Client::get();
        $products = Product::with(['brand', 'latestPurchase'])->get()->map(function ($product) {
            $price = 0;
            if ($product->latestPurchase && $product->latestPurchase->unit_price > 0) {
                $price = (float)$product->latestPurchase->unit_price;
            } else {
                $lastSaleItem = \App\Models\SalesItem::where('product_id', $product->id)->latest()->first();
                if ($lastSaleItem && $lastSaleItem->unit_price > 0) {
                    $price = (float)$lastSaleItem->unit_price;
                } else {
                    $lastProjectItem = \App\Models\ProjectItem::where('product_id', $product->id)->latest()->first();
                    if ($lastProjectItem && $lastProjectItem->unit_price > 0) {
                        $price = (float)$lastProjectItem->unit_price;
                    }
                }
            }
            $product->calculated_purchase_price = $price;
            return $product;
        });
        $companyDetails = CompanyDetail::where('is_active', true)->get();
        return view('frontend.pages.quotations.create', compact('clients', 'products', 'companyDetails'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'client_id' => 'nullable|exists:clients,id',
        'client_name' => 'required|string|max:255',
        'client_designation' => 'nullable|string|max:255',
        'client_address' => 'required|string',
        'client_phone' => 'nullable|string|max:20',
        'client_email' => 'nullable|email|max:255',
        'attention_to' => 'nullable|string|max:255',
        'body_content' => 'required|string',
        'terms_conditions' => 'required|string',
        'subject' => 'required|string|max:255',
        'company_name' => 'required|string|max:255',
        'signatory_name' => 'required|string|max:255',
        'signatory_designation' => 'required|string|max:255',
        'company_phone' => 'nullable|string|max:20',
        'company_email' => 'nullable|email|max:255',
        'company_website' => 'nullable|string|max:255',
        'additional_enclosed' => 'nullable|string',
        'discount_amount' => 'nullable|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.description' => 'nullable|string',
    ]);

    DB::transaction(function () use ($request, $validated) {
        $subTotal = 0;
        foreach ($request->items as $item) {
            $subTotal += $item['quantity'] * $item['unit_price'];
        }

        $discountAmount = $request->discount_amount ?? 0;
        $totalAmount = $subTotal - $discountAmount;

        // Create quotation with all client and company details
        $quotation = Quotation::create([
            'client_id' => $request->client_id,
            'client_name' => $request->client_name,
            'client_designation' => $request->client_designation,
            'client_address' => $request->client_address,
            'client_phone' => $request->client_phone,
            'client_email' => $request->client_email,
            'attention_to' => $request->attention_to,
            'body_content' => $request->body_content,
            'terms_conditions' => $request->terms_conditions,
            'subject' => $request->subject,
            'company_name' => $request->company_name,
            'signatory_name' => $request->signatory_name,
            'signatory_designation' => $request->signatory_designation,
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email,
            'company_website' => $request->company_website,
            'additional_enclosed' => $request->additional_enclosed,
            'quotation_date' => now(),
            'expiry_date' => now()->addDays(15),
            'notes' => $request->subject,
            'sub_total' => $subTotal,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'show_signature' => $request->has('show_signature') ? (bool)$request->show_signature : true,
            'show_seal' => $request->has('show_seal') ? (bool)$request->show_seal : true,
        ]);

        // Create quotation items
        foreach ($request->items as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['quantity'] * $item['unit_price'],
                'description' => $item['description'] ?? null,
            ]);
        }

        // No need to store in session anymore - all data is in database
    });

    return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');
}
    public function show(Quotation $quotation)
    {
        $attention_to = $quotation->attention_to;
        $quotation->load(['client', 'items.product.brand']);
        return view('frontend.pages.quotations.show', compact('quotation', 'attention_to'));
    }

    public function edit(Quotation $quotation)
    {
        $clients = Client::get();
        $products = Product::with(['brand', 'latestPurchase'])->get()->map(function ($product) {
            $price = 0;
            if ($product->latestPurchase && $product->latestPurchase->unit_price > 0) {
                $price = (float)$product->latestPurchase->unit_price;
            } else {
                $lastSaleItem = \App\Models\SalesItem::where('product_id', $product->id)->latest()->first();
                if ($lastSaleItem && $lastSaleItem->unit_price > 0) {
                    $price = (float)$lastSaleItem->unit_price;
                } else {
                    $lastProjectItem = \App\Models\ProjectItem::where('product_id', $product->id)->latest()->first();
                    if ($lastProjectItem && $lastProjectItem->unit_price > 0) {
                        $price = (float)$lastProjectItem->unit_price;
                    }
                }
            }
            $product->calculated_purchase_price = $price;
            return $product;
        });
        $quotation->load('items');
        
        $companyDetails = CompanyDetail::where('is_active', true)->get();
        return view('frontend.pages.quotations.edit', compact('quotation', 'clients', 'products', 'companyDetails'));
    }

    public function update(Request $request, Quotation $quotation)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'client_name' => 'required|string|max:255',
            'client_designation' => 'nullable|string|max:255',
            'client_address' => 'required|string',
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'attention_to' => 'nullable|string|max:255',
            'body_content' => 'required|string',
            'terms_conditions' => 'required|string',
            'subject' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'signatory_name' => 'required|string|max:255',
            'signatory_designation' => 'required|string|max:255',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|string|max:255',
            'additional_enclosed' => 'nullable|string',
            'quotation_date' => 'required|date',
            'expiry_date' => 'required|date|after:quotation_date',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $quotation) {
            // Delete existing items
            $quotation->items()->delete();

            $subTotal = 0;

            // Calculate new subtotal
            foreach ($request->items as $item) {
                $subTotal += $item['quantity'] * $item['unit_price'];
            }

            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $subTotal - $discountAmount;

            // Update quotation with all details
            $quotation->update([
                'client_id' => $request->client_id,
                'client_name' => $request->client_name,
                'client_designation' => $request->client_designation,
                'client_address' => $request->client_address,
                'client_phone' => $request->client_phone,
                'client_email' => $request->client_email,
                'attention_to' => $request->attention_to,
                'body_content' => $request->body_content,
                'terms_conditions' => $request->terms_conditions,
                'subject' => $request->subject,
                'company_name' => $request->company_name,
                'signatory_name' => $request->signatory_name,
                'signatory_designation' => $request->signatory_designation,
                'company_phone' => $request->company_phone,
                'company_email' => $request->company_email,
                'company_website' => $request->company_website,
                'additional_enclosed' => $request->additional_enclosed,
                'quotation_date' => $request->quotation_date,
                'expiry_date' => $request->expiry_date,
                'notes' => $request->notes,
                'sub_total' => $subTotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'show_signature' => $request->has('show_signature') ? (bool)$request->show_signature : true,
                'show_seal' => $request->has('show_seal') ? (bool)$request->show_seal : true,
            ]);

            // Create new items
            foreach ($request->items as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                    'description' => $item['description'] ?? null,
                ]);
            }
        });

        return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }

public function preview(Quotation $quotation)
{
    $quotation->load(['client', 'items.product.brand']);
    $amount_in_words = $this->convertNumberToWords($quotation->total_amount). ' Taka Only';
    
    // Look up signature image from CompanyDetail
    $signatoryName = $quotation->signatory_name ?? '';
    $companyDetail = CompanyDetail::where('signatory_name', $signatoryName)->first();

    $data = [
        'quotation' => $quotation,
        'amount_in_words' => $amount_in_words,
        'client_name' => $quotation->client_name ?? ($quotation->client?->name ?? ''),
        'client_designation' => $quotation->client_designation ?? '',
        'client_address' => $quotation->client_address ?? ($quotation->client?->address ?? ''),
        'client_phone' => $quotation->client_phone ?? ($quotation->client?->phone ?? ''),
        'client_email' => $quotation->client_email ?? ($quotation->client?->email ?? ''),
        'attention_to' => $quotation->attention_to ?? '',
        'body_content' => $quotation->body_content ?? '',
        'terms_conditions' => $quotation->terms_conditions ?? '',
        'subject' => $quotation->subject ?? '',
        'company_name' => $quotation->company_name ?? 'N/A',
        'signatory_name' => $quotation->signatory_name ?? 'N/A',
        'signatory_designation' => $quotation->signatory_designation ?? 'N/A',
        'company_phone' => $quotation->company_phone ?? 'N/A',
        'company_email' => $quotation->company_email ?? 'N/A',
        'company_website' => $quotation->company_website ?? 'N/A',
        'company' => [
            'name' => $quotation->company_name ?? 'N/A',
            'signatory_name' => $quotation->signatory_name ?? 'N/A',
            'signatory_designation' => $quotation->signatory_designation ?? 'N/A',
            'phone' => $quotation->company_phone ?? 'N/A',
            'email' => $quotation->company_email ?? 'N/A',
            'website' => $quotation->company_website ?? 'N/A',
            'signature_image' => $companyDetail->signature_image ?? null,
            'seal_image' => $companyDetail->seal_image ?? null,
        ],
        'additional_enclosed' => $quotation->additional_enclosed ?? '',
        'show_signature' => $quotation->show_signature ?? true,
        'show_seal' => $quotation->show_seal ?? true,
        'signature_image' => $companyDetail->signature_image ?? null,
        'seal_image' => $companyDetail->seal_image ?? null,
    ];
    
    $html = view('pdf.quotations', $data)->render();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'Helvetica',
    ]);
    $mpdf->WriteHTML($html);

    return response($mpdf->Output('Quotation_' . ($quotation->quotation_number ?? $quotation->id) . '.pdf', 'I'), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}

public function download(Quotation $quotation)
{
    $quotation->load(['client', 'items.product.brand']);
    $amount_in_words = $this->convertNumberToWords($quotation->total_amount). ' Taka Only';
    
    // Look up signature image from CompanyDetail
    $signatoryName = $quotation->signatory_name ?? '';
    $companyDetail = CompanyDetail::where('signatory_name', $signatoryName)->first();

    // Prepare data from database
    $data = [
        'quotation' => $quotation,
        'amount_in_words' => $amount_in_words,
        'client_name' => $quotation->client_name ?? ($quotation->client?->name ?? ''),
        'client_designation' => $quotation->client_designation ?? '',
        'client_address' => $quotation->client_address ?? ($quotation->client?->address ?? ''),
        'client_phone' => $quotation->client_phone ?? ($quotation->client?->phone ?? ''),
        'client_email' => $quotation->client_email ?? ($quotation->client?->email ?? ''),
        'attention_to' => $quotation->attention_to ?? '',
        'body_content' => $quotation->body_content ?? '',
        'terms_conditions' => $quotation->terms_conditions ?? '',
        'subject' => $quotation->subject ?? '',
        'company_name' => $quotation->company_name ?? '',
        'signatory_name' => $quotation->signatory_name ?? '',
        'signatory_designation' => $quotation->signatory_designation ?? '',
        'company_phone' => $quotation->company_phone ?? '',
        'company_email' => $quotation->company_email ?? '',
        'company_website' => $quotation->company_website ?? '',
        'company' => [
            'name' => $quotation->company_name ?? 'Intelligent Technology',
            'signatory_name' => $quotation->signatory_name ?? 'Engr. Shamsul Alam',
            'signatory_designation' => $quotation->signatory_designation ?? 'Director (Technical)',
            'phone' => $quotation->company_phone ?? '',
            'email' => $quotation->company_email ?? '',
            'website' => $quotation->company_website ?? '',
            'signature_image' => $companyDetail->signature_image ?? null,
            'seal_image' => $companyDetail->seal_image ?? null,
        ],
        'additional_enclosed' => $quotation->additional_enclosed ?? '',
        'show_signature' => $quotation->show_signature ?? true,
        'show_seal' => $quotation->show_seal ?? true,
        'signature_image' => $companyDetail->signature_image ?? null,
        'seal_image' => $companyDetail->seal_image ?? null,
    ];
    
    $html = view('pdf.quotations', $data)->render();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'Helvetica',
    ]);
    $mpdf->WriteHTML($html);

    $fileRecipientName = $quotation->client_name ?? $quotation->client?->name ?? 'client';
    $clientSlug = Str::slug($fileRecipientName);
    $quotationDate = $quotation->quotation_date
        ? Carbon::parse($quotation->quotation_date)->format('d-m-Y')
        : now()->format('d-m-Y');
    $fileName = $clientSlug . '-' . $quotationDate . '.pdf';

    return response($mpdf->Output($fileName, 'I'), 200, [
        'Content-Type' => 'application/pdf',
    ]);

}

public function generatePDF(Quotation $quotation)
{
    $quotation->load(['items.product.brand']);

    $amount_in_words = $this->convertNumberToWords($quotation->total_amount) . ' Taka Only';

    // Use data from database instead of session
    $data = [
        'quotation' => $quotation,
        'amount_in_words' => $amount_in_words,
        'client_name' => $quotation->client_name ?? '',
        'client_designation' => $quotation->client_designation ?? '',
        'client_address' => $quotation->client_address ?? '',
        'client_phone' => $quotation->client_phone ?? '',
        'client_email' => $quotation->client_email ?? '',
        'attention_to' => $quotation->attention_to ?? '',
        'body_content' => $quotation->body_content ?? '',
        'terms_conditions' => $quotation->terms_conditions ?? '',
        'subject' => $quotation->subject ?? '',
        'company_name' => $quotation->company_name ?? '',
        'signatory_name' => $quotation->signatory_name ?? '',
        'signatory_designation' => $quotation->signatory_designation ?? '',
        'company_phone' => $quotation->company_phone ?? '',
        'company_email' => $quotation->company_email ?? '',
        'company_website' => $quotation->company_website ?? '',
        'additional_enclosed' => $quotation->additional_enclosed ?? '',
    ];

    $html = view('pdf.quotations', $data)->render();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'Helvetica',
    ]);
    $mpdf->WriteHTML($html);
    $fileRecipientName = $quotation->client_name ?? $quotation->client?->name ?? 'client';
    $clientSlug = Str::slug($fileRecipientName);
    $quotationDate = $quotation->quotation_date
        ? Carbon::parse($quotation->quotation_date)->format('d-m-Y')
        : now()->format('d-m-Y');
    $fileName = $clientSlug . '-' . $quotationDate . '.pdf';

    return response($mpdf->Output($fileName, 'I'), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}
    public function sendQuotation(Quotation $quotation)
    {
        $quotation->update(['status' => 'sent']);
        return redirect()->back()->with('success', 'Quotation sent successfully.');
    }

    public function updateStatus(Request $request, Quotation $quotation)
    {
        $request->validate([
            'status' => 'required|in:sent,accepted,rejected,expired'
        ]);

        $quotation->update(['status' => $request->status]);
        return redirect()->back()->with('success', 'Quotation status updated successfully.');
    }

    public function getProduct($id)
    {
        $product = Product::with('brand')->findOrFail($id);
        return response()->json([
            'product' => $product,
            'unit_price' => $product->price ?? 0 
        ]);
    }

    private function convertNumberToWords($number)
{
    if ($number == 0) {
        return 'Zero';
    }
    
    $ones = array(
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen'
    );
    
    $tens = array(
        2 => 'Twenty',
        3 => 'Thirty',
        4 => 'Forty',
        5 => 'Fifty',
        6 => 'Sixty',
        7 => 'Seventy',
        8 => 'Eighty',
        9 => 'Ninety'
    );
    
    $words = '';

    // Handle crores
    if ($number >= 10000000) {
        $crores = floor($number / 10000000);
        $words .= $this->convertNumberToWords($crores) . ' Crore ';
        $number %= 10000000; // ADDED THIS LINE
    } // ADDED MISSING CLOSING BRACE HERE
    
    // Handle lakhs
    if ($number >= 100000) {
        $lakhs = floor($number / 100000);
        $words .= $this->convertNumberToWords($lakhs) . ' Lakh ';
        $number %= 100000;
    }
    
    // Handle thousands
    if ($number >= 1000) {
        $thousands = floor($number / 1000);
        $words .= $this->convertNumberToWords($thousands) . ' Thousand ';
        $number %= 1000;
    }
    
    // Handle hundreds
    if ($number >= 100) {
        $hundreds = floor($number / 100);
        $words .= $this->convertNumberToWords($hundreds) . ' Hundred ';
        $number %= 100;
    }
    
    // Handle tens and ones
    if ($number > 0) {
        if ($number < 20) {
            $words .= $ones[$number];
        } else {
            $words .= $tens[floor($number / 10)];
            if ($number % 10 > 0) {
                $words .= ' ' . $ones[$number % 10];
            }
        }
    }
    
    return trim($words);
}

public function reportPdf(Request $request)
{
    $query = Quotation::with('client');

    if ($request->filled('date_from')) {
        $query->whereDate('quotation_date', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('quotation_date', '<=', $request->date_to);
    }

    $quotations = $query->latest()->get();

    $html = view('pdf.quotations-report', compact('quotations', 'request'))->render();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font' => 'Helvetica',
    ]);
    $mpdf->WriteHTML($html);
    return response($mpdf->Output('quotations-report.pdf', 'I'), 200, [
        'Content-Type' => 'application/pdf',
    ]);
}
}
