<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Invoice #{{ $sales->order_no }}</title>
    @php
        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';
    @endphp
    <style>
        @page {
            @if($padBase64)
            background-image: url('{{ $padBase64 }}');
            background-image-resize: 6;
            @endif
            margin-top: 42mm;
            margin-bottom: 15mm;
            margin-left: 15mm;
            margin-right: 15mm;
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
                <h1 style="font-size: 26px; color: #0f172a; margin-bottom: 4px;">INVOICE</h1>
                <div style="font-size: 15px; font-weight: 700; color: #4f46e5;">Invoice No: #{{ $sales->order_no }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 3px;">Invoice Date: {{ $sales->created_at ? $sales->created_at->format('d M Y') : date('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- Customer Info Card -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; margin-bottom: 25px;">
        <tr>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">CUSTOMER / CLIENT</div>
                <div style="font-size: 13px; color: #0f172a;">{{ $customer->name ?? 'N/A' }}</div>
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">PHONE NUMBER</div>
                <div style="font-size: 13px; color: #0f172a;">{{ $customer->phone ?? 'N/A' }}</div>
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">ADDRESS / LOCATION</div>
                <div style="font-size: 13px; color: #0f172a;">{{ $customer->address ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 25px; border: 1px solid #cbd5e1; border-radius: 12px; overflow: hidden;">
        <thead>
            <tr>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 6%;">#</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; width: 48%;">Item Description &amp; Model</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 12%;">Qty</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 17%;">Unit Price</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 17%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td style="padding: 10px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: center;">{{ $loop->index + 1 }}</td>
                    <td style="padding: 10px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ $item->name }}</strong>
                        @if(!empty($item->model))
                            <div style="font-size: 10px; color: #64748b;">Model: {{ $item->model }}</div>
                        @endif
                    </td>
                    <td style="padding: 10px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: center;">{{ $item->qty ?? 1 }}</td>
                    <td style="padding: 10px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: right;">{{ $item->unit_price ? number_format($item->unit_price, 2) : '0.00' }}</td>
                    <td style="padding: 10px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: right;">{{ $item->total_price ? number_format($item->total_price, 2) : '0.00' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $completedReturns = $returns->where('status', 'completed');
        $totalRefundAmount = $completedReturns->sum(function($return) {
            return $return->total_refund_amount ?? $return->items->sum('total_price');
        });
        $hasReturns = $completedReturns->count() > 0;
    @endphp

    @if($hasReturns)
    <!-- Returns Section -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #fff5f5; border: 1px dashed #f87171; border-radius: 12px; margin-bottom: 25px;">
        <tr>
            <td style="padding: 14px 18px;">
                <h4 style="color: #dc2626; font-size: 13px; font-weight: 700; margin-bottom: 10px;">Returned Items</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="background-color: #dc2626; color: white; padding: 8px; font-size: 11px;">Return #</th>
                            <th style="background-color: #dc2626; color: white; padding: 8px; font-size: 11px;">Product</th>
                            <th style="background-color: #dc2626; color: white; padding: 8px; font-size: 11px; text-align: center;">Qty</th>
                            <th style="background-color: #dc2626; color: white; padding: 8px; font-size: 11px;">Reason</th>
                            <th style="background-color: #dc2626; color: white; padding: 8px; font-size: 11px;">Condition</th>
                            <th style="background-color: #dc2626; color: white; padding: 8px; font-size: 11px; text-align: right;">Refund Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedReturns as $return)
                            @foreach($return->items as $returnItem)
                                <tr>
                                    <td style="padding: 8px; font-size: 11px; border-bottom: 1px solid #fecaca;">#{{ $return->id }}</td>
                                    <td style="padding: 8px; font-size: 11px; border-bottom: 1px solid #fecaca;">{{ $returnItem->product->name ?? 'N/A' }}</td>
                                    <td style="padding: 8px; font-size: 11px; border-bottom: 1px solid #fecaca; text-align: center;">{{ $returnItem->quantity }}</td>
                                    <td style="padding: 8px; font-size: 11px; border-bottom: 1px solid #fecaca;">{{ $returnItem->reason_label }}</td>
                                    <td style="padding: 8px; font-size: 11px; border-bottom: 1px solid #fecaca;">{{ $returnItem->condition_label }}</td>
                                    <td style="padding: 8px; font-size: 11px; border-bottom: 1px solid #fecaca; text-align: right;">{{ number_format($returnItem->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    @endif

    <!-- Summary & Terms Grid (Rounded Card Tables) -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 25px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 10px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                    <tr>
                        <td style="padding: 16px;">
                            <h3 style="font-size: 13px; font-weight: 700; color: #0f172a;">Terms &amp; Conditions</h3> <br>
                            <p style="font-size: 11px; color: #475569; line-height: 1.5; margin-bottom: 6px;">&bull; Products can be returned within 7 days in their original, unopened condition.</p> <br>
                            <p style="font-size: 11px; color: #475569; line-height: 1.5; margin-bottom: 8px;">&bull; Refunds or exchanges are offered, but perishable/custom items cannot be returned.</p> <br>
                            <div>
                                Contact support at <strong style="color: #4f46e5;">01904400205</strong> for valid receipt claims.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 10px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                    <tr>
                        <td style="padding: 16px;">
                            <!-- <h3 style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 8px;">Invoice Summary</h3> <br> -->
                            <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                                @if($hasReturns)
                                <tr style="color: #94a3b8; text-decoration: line-through;">
                                    <td style="padding: 4px 0;">Original Sub Total:</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 600;">{{ number_format($sales->bill + $totalRefundAmount, 2) }}</td>
                                </tr>
                                <tr style="color: #dc2626;">
                                    <td style="padding: 4px 0;">Returns / Refund:</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 600;">- {{ number_format($totalRefundAmount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 4px 0; color: #475569;">Sub Total:</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ number_format($sales->bill, 2) }}</td>
                                </tr>
                                @if(($sales->vat ?? 0) > 0)
                                @php $vatAmount = ($sales->bill * $sales->vat) / 100; @endphp
                                <tr>
                                    <td style="padding: 4px 0; color: #475569;">VAT ({{ number_format($sales->vat, 2) }}%):</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ number_format($vatAmount, 2) }}</td>
                                </tr>
                                @endif
                                @if(($sales->tax ?? 0) > 0)
                                @php $taxAmount = ($sales->bill * $sales->tax) / 100; @endphp
                                <tr>
                                    <td style="padding: 4px 0; color: #475569;">Tax ({{ number_format($sales->tax, 2) }}%):</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ number_format($taxAmount, 2) }}</td>
                                </tr>
                                @endif
                                @if(($sales->delivery_charge ?? 0) > 0)
                                <tr>
                                    <td style="padding: 4px 0; color: #475569;">Delivery Charge:</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ number_format($sales->delivery_charge, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 4px 0; color: #475569;">Discount:</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ number_format($sales->discount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 0; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; font-size: 13px; font-weight: 800; color: #4f46e5;">Grand Total:</td>
                                    <td style="padding: 6px 0; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; text-align: right; font-size: 13px; font-weight: 800; color: #4f46e5;">{{ number_format($sales->payble, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; color: #475569;">Received Amount:</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ number_format($sales->advanced_payment ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-weight: 800; color: #dc2626;">Total Due:</td>
                                    <td style="padding: 4px 0; text-align: right; font-weight: 800; color: {{ ($sales->due_payment ?? 0) > 0 ? '#dc2626' : '#16a34a' }};">{{ number_format($sales->due_payment ?? 0, 2) }}</td>
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
                @php $totalAmount = $sales->bill ?? 0; @endphp
                {{ numberToWords($totalAmount) }} Taka Only
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 60px;">
        <tr>
            <td width="50%" align="center" style="vertical-align: bottom;">
                <table align="center" style="width: 180px; margin: 0 auto 8px auto; border-collapse: collapse;">
                    <tr>
                        <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size: 11px; font-weight: 600; color: #475569;">Customer Signature</div>
            </td>
            <td width="50%" align="center" style="vertical-align: bottom;">
                <table align="center" style="width: 180px; margin: 0 auto 8px auto; border-collapse: collapse;">
                    <tr>
                        <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size: 11px; font-weight: 600; color: #475569;">Authorized Signature</div>
            </td>
        </tr>
    </table>

</body>
</html>