<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Service Invoice #{{ $service->service_no ?? $service->id }}</title>
    @php
        $padPath = public_path('assets/invoice/final_pad.png');
        $padBase64 = file_exists($padPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($padPath)) : '';
        $payableTotal = max(0, ($service->bill ?? 0) - ($service->discount ?? 0));
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
                <h1 style="font-size: 24px; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.5px;">SERVICE INVOICE</h1>
                <div style="font-size: 14px; font-weight: 700; color: #4f46e5;">Invoice No: #{{ $service->service_no ?? $service->id }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 3px;">
                    Service Date: {{ $service->created_at ? $service->created_at->format('d M Y') : date('d M Y') }}
                </div>
                <div style="margin-top: 6px; font-size: 11px; color: #64748b;">
                    Payment Status: 
                    @if(($service->due_amount ?? 0) <= 0)
                        <strong style="color: #16a34a;">PAID</strong>
                    @elseif(($service->paid_amount ?? 0) > 0)
                        <strong style="color: #d97706;">PARTIALLY PAID</strong>
                    @else
                        <strong style="color: #dc2626;">DUE / UNPAID</strong>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer / Client Info Card -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: transparent; border: 1px solid #cbd5e1; border-radius: 12px; margin-bottom: 25px;">
        <tr>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">CUSTOMER / CLIENT</div>
                <div style="font-size: 13px; font-weight: 700; color: #0f172a;">{{ $service->name ?? 'N/A' }}</div>
                @if(!empty($service->email))
                    <div style="font-size: 11px; color: #475569; margin-top: 2px;">{{ $service->email }}</div>
                @endif
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">CONTACT DETAILS</div>
                <div style="font-size: 12px; color: #0f172a;"><strong>Phone:</strong> {{ $service->phone ?? 'N/A' }}</div>
                @if(!empty($service->warranty_duration))
                    <div style="font-size: 11px; color: #475569; margin-top: 2px;"><strong>Warranty:</strong> {{ $service->warranty_duration }}</div>
                @endif
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">LOCATION / ADDRESS</div>
                <div style="font-size: 12px; color: #0f172a;">{{ $service->address ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <!-- Service Items Table -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 25px; border: 1px solid #cbd5e1; border-radius: 12px; overflow: hidden;">
        <thead>
            <tr>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 6%;">#</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; width: 48%;">Service / Item Description</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; width: 12%;">Qty</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 17%;">Unit Price</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 17%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @php $itemIndex = 1; @endphp
            <tr style="background-color: #ffffff;">
                <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: center; vertical-align: top;">{{ $itemIndex }}</td>
                <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: top;">
                    <div style="font-weight: 700; color: #0f172a; font-size: 13px;">{{ $service->product_name ?? ($service->product->name ?? 'Service / Repairing Job') }}</div>
                    @if(!empty($service->product_number))
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Tag / S/N: {{ $service->product_number }}</div>
                    @endif
                    @if(!empty($service->details))
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Work Details: {{ $service->details }}</div>
                    @endif
                    @if(!empty($service->remarks))
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">Remarks: {{ $service->remarks }}</div>
                    @endif
                </td>
                <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: center; vertical-align: top;">
                    <span style="font-weight: 700;">1</span>
                </td>
                <td style="padding: 12px 14px; font-size: 12px; color: #334155; border-bottom: 1px solid #f1f5f9; text-align: right; vertical-align: top;">
                    {{ number_format($service->bill ?? 0, 2) }}
                </td>
                <td style="padding: 12px 14px; font-size: 12px; color: #0f172a; border-bottom: 1px solid #f1f5f9; text-align: right; font-weight: 700; vertical-align: top;">
                    {{ number_format($service->bill ?? 0, 2) }}
                </td>
            </tr>
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
                                    <td style="padding: 5px 0; text-align: right; font-weight: 600; color: #0f172a;">{{ number_format($service->bill ?? 0, 2) }}</td>
                                </tr>
                                @if(($service->discount ?? 0) > 0)
                                <tr>
                                    <td style="padding: 5px 0; color: #475569;">Discount:</td>
                                    <td style="padding: 5px 0; text-align: right; font-weight: 600; color: #ef4444;">-{{ number_format($service->discount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 7px 0; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; font-size: 13px; font-weight: 800; color: #4f46e5;">Grand Total:</td>
                                    <td style="padding: 7px 0; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; text-align: right; font-size: 13px; font-weight: 800; color: #4f46e5;">{{ number_format($payableTotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0; color: #475569;">Received Amount:</td>
                                    <td style="padding: 5px 0; text-align: right; font-weight: 700; color: #16a34a;">{{ number_format($service->paid_amount ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0; font-weight: 800; color: #dc2626;">Total Due:</td>
                                    <td style="padding: 5px 0; text-align: right; font-weight: 800; color: {{ ($service->due_amount ?? 0) > 0 ? '#dc2626' : '#16a34a' }};">{{ number_format($service->due_amount ?? 0, 2) }}</td>
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
                {{ function_exists('numberToWords') ? numberToWords($payableTotal) : $payableTotal }} Taka Only
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
                <div style="font-size: 11px; font-weight: 600; color: #475569;">Customer Signature</div>
            </td>
            <td width="50%" align="center" style="vertical-align: top;">
                <table align="center" style="width: 180px; margin: 0 auto 8px auto; border-collapse: collapse;">
                    <tr>
                        <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size: 11px; font-weight: 600; color: #4f46e5;">Authorized Signature</div>
            </td>
        </tr>
    </table>

    <!-- Footer fixed at bottom right above pad graphic -->
    <htmlpagefooter name="invoiceFooter">
        @if(!empty($service->repairedBy))
        <div style="text-align: right; font-size: 10.5px; color: #64748b;">
            Repaired By: <strong style="color: #0f172a;">{{ $service->repairedBy->name }}</strong>
        </div>
        @endif
    </htmlpagefooter>

</body>
</html>
