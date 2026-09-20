<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Purchase Invoice #{{ $purchaseNo ?? ('PUR-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT)) }}</title>
    @php
        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';
        $itemsList = isset($items) && count($items) > 0 ? $items : collect([$purchase]);
        $invNo = $purchaseNo ?? ('PUR-' . str_pad($purchase->id, 5, '0', STR_PAD_LEFT));
        $dateVal = $createdAt ?? ($purchase->created_at ?? now());
        $dueVal = $totalDue ?? ($purchase->due ?? 0);
        $paidVal = $totalPayment ?? ($purchase->payment ?? 0);
        $subVal = $subTotal ?? ($purchase->sub_price ?? ($purchase->quantity * $purchase->unit_price));
        $totalVal = $totalAmount ?? ($purchase->total_price ?? 0);
        $discountVal = $totalDiscount ?? max(0, $subVal - $totalVal);
    @endphp
    <style>
        @page {
            @if($padBase64)
            background-image: url('{{ $padBase64 }}');
            background-image-resize: 6;
            @endif
            margin-top: 42mm;
            margin-bottom: 32mm;
            margin-left: 15mm;
            margin-right: 15mm;
            footer: invoiceFooter;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Helvetica, Arial, sans-serif;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #0f172a;
            line-height: 1.4;
        }
    </style>
</head>

<body>
    <!-- Header Title & Order Meta -->
    <table style="width: 100%; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px;">
        <tr>
            <td align="right">
                <h1 style="font-size: 24px; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.5px;">PURCHASE INVOICE</h1>
                <div style="font-size: 14px; font-weight: 700; color: #4f46e5;">Invoice No: #{{ $invNo }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                    Purchase Date: {{ \Carbon\Carbon::parse($dateVal)->format('d M Y') }}
                </div>
                <div style="margin-top: 6px; font-size: 11px; color: #64748b;">
                    Payment Status: 
                    @if($dueVal <= 0)
                        <strong style="color: #16a34a;">PAID</strong>
                    @elseif($paidVal > 0)
                        <strong style="color: #d97706;">PARTIALLY PAID</strong>
                    @else
                        <strong style="color: #dc2626;">DUE / UNPAID</strong>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Vendor / Supplier Info Card -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: transparent; border: 1px solid #cbd5e1; border-radius: 12px; margin-bottom: 25px;">
        <tr>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">VENDOR / SUPPLIER</div>
                <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $vendor->name ?? 'N/A' }}</div>
                @if(!empty($vendor->company_name))
                    <div style="font-size: 11px; color: #475569; margin-top: 2px;">{{ $vendor->company_name }}</div>
                @endif
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">CONTACT DETAILS</div>
                <div style="font-size: 12px; color: #0f172a;"><strong>Phone:</strong> {{ $vendor->phone ?? 'N/A' }}</div>
                @if(!empty($vendor->email))
                    <div style="font-size: 11px; color: #475569; margin-top: 2px;"><strong>Email:</strong> {{ $vendor->email }}</div>
                @endif
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">LOCATION / ADDRESS</div>
                <div style="font-size: 12px; color: #0f172a;">{{ $vendor->address ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 25px; border: 1px solid #cbd5e1; border-radius: 12px; overflow: hidden;">
        <thead>
            <tr>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 6%;">#</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; width: 48%;">Product &amp; Specification</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 12%;">Qty</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 17%;">Unit Price</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 17%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($itemsList as $index => $item)
                @php
                    $prod = $item->product;
                    $itemSerials = $item->serials;
                @endphp
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: center; vertical-align: top;">
                        {{ $index + 1 }}
                    </td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: top;">
                        <div style="font-weight: 700; color: #0f172a; font-size: 13px;">{{ $prod->name ?? 'Product Not Found' }}</div>
                        @if(!empty($prod->model))
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Model: {{ $prod->model }}</div>
                        @endif
                        @if(!empty($prod->brand?->name))
                            <div style="font-size: 11px; color: #64748b;">Brand: {{ $prod->brand->name }}</div>
                        @endif

                        @if(isset($itemSerials) && $itemSerials->count() > 0)
                            <div style="margin-top: 6px; padding: 6px 8px; background: #f1f5f9; border-radius: 6px; font-size: 10px; color: #334155;">
                                <strong style="color: #4f46e5;">Registered Serials ({{ $itemSerials->count() }}):</strong>
                                <div style="margin-top: 2px; font-family: monospace; word-break: break-all;">
                                    {{ $itemSerials->pluck('serial_number')->implode(', ') }}
                                </div>
                            </div>
                        @endif
                    </td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: center; vertical-align: top;">
                        <span style="font-weight: 700;">{{ $item->quantity }}</span> Pcs
                    </td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: right; vertical-align: top;">
                        {{ number_format($item->unit_price, 2) }}
                    </td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #0f172a; border-bottom: 1px solid #f1f5f9; text-align: right; font-weight: 700; vertical-align: top;">
                        {{ number_format($item->total_price, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Grid -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 25px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                &nbsp;
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                    <tr>
                        <td style="padding: 16px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                <tr>
                                    <td style="padding: 5px 0; color: #475569;">Sub Total:</td>
                                    <td style="padding: 5px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ number_format($subVal, 2) }}</td>
                                </tr>
                                @if($discountVal > 0)
                                <tr>
                                    <td style="padding: 5px 0; color: #e11d48;">Discount:</td>
                                    <td style="padding: 5px 0; text-align: right; font-weight: 600; color: #e11d48;">-{{ number_format($discountVal, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 7px 0; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; font-size: 13px; font-weight: 800; color: #4f46e5;">Total Amount:</td>
                                    <td style="padding: 7px 0; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; text-align: right; font-size: 13px; font-weight: 800; color: #4f46e5;">{{ number_format($totalVal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0; color: #475569;">Paid Amount:</td>
                                    <td style="padding: 5px 0; text-align: right; font-weight: 700; color: #16a34a;">{{ number_format($paidVal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0; font-weight: 800; color: #dc2626;">Due Balance:</td>
                                    <td style="padding: 5px 0; text-align: right; font-weight: 800; color: {{ $dueVal > 0 ? '#dc2626' : '#16a34a' }};">{{ number_format($dueVal, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- In Words Card -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; margin-bottom: 40px;">
        <tr>
            <td style="padding: 10px 16px; font-size: 12px; color: #334155;">
                <strong style="color: #4f46e5; margin-right: 6px;">Amount In Words:</strong>
                {{ function_exists('numberToWords') ? numberToWords($totalVal) : '' }} Taka Only
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 60px;">
        <tr>
            <td width="50%" align="center" style="vertical-align: top;">
                <table align="center" style="width: 180px; margin: 0 auto 8px auto; border-collapse: collapse;">
                    <tr>
                        <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size: 11px; font-weight: 600; color: #475569;">Vendor / Supplier Signature</div>
            </td>
            <td width="50%" align="center" style="vertical-align: top;">
                <table align="center" style="width: 180px; margin: 0 auto 8px auto; border-collapse: collapse;">
                    <tr>
                        <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size: 11px; font-weight: 600; color: #4f46e5;">Authorized Receiver Signature</div>
            </td>
        </tr>
    </table>

    <!-- Footer fixed at bottom right above pad graphic -->
    <htmlpagefooter name="invoiceFooter">
        @if(!empty($creator ?? $purchase->creator))
        <div style="text-align: right; font-size: 10.5px; color: #64748b;">
            Purchased By: <strong style="color: #0f172a;">{{ ($creator ?? $purchase->creator)->name }}</strong>
        </div>
        @endif
    </htmlpagefooter>

</body>
</html>
