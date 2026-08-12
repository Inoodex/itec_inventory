<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Service Invoice #{{ $service->service_no ?? $service->id }}</title>
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
            margin-top: 45mm;
            margin-bottom: 25mm;
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
                <h1 style="font-size: 26px; color: #0f172a; margin-bottom: 4px;">SERVICE INVOICE</h1>
                <div style="font-size: 15px; font-weight: 700; color: #4f46e5;">Invoice No: #{{ $service->service_no ?? $service->id }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 3px;">Invoice Date: {{ $service->created_at ? $service->created_at->format('d M Y') : date('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- Customer Info Card -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; margin-bottom: 25px;">
        <tr>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">CUSTOMER / CLIENT</div>
                <div style="font-size: 13px; color: #0f172a; font-weight: 600;">{{ $service->name ?? 'N/A' }}</div>
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">PHONE NUMBER</div>
                <div style="font-size: 13px; color: #0f172a;">{{ $service->phone ?? 'N/A' }}</div>
            </td>
            <td style="padding: 14px 18px; width: 33.33%; vertical-align: top; border-left: 1px solid #cbd5e1;">
                <div style="font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #64748b; margin-bottom: 4px;">ADDRESS / LOCATION</div>
                <div style="font-size: 13px; color: #0f172a;">{{ $service->address ?? 'N/A' }}</div>
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
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 17%;">Price</th>
                <th style="background-color: #1e293b; color: #ffffff; padding: 10px 14px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; text-align: right; width: 17%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                <tr style="background-color: {{ $index % 2 == 1 ? '#f8fafc' : '#ffffff' }};">
                    <td style="padding: 12px 14px; font-size: 12px; color: #475569; text-align: center; border-bottom: 1px solid #e2e8f0;">{{ $index + 1 }}</td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #0f172a; border-bottom: 1px solid #e2e8f0;">
                        <div style="font-weight: 600;">{{ $item->name }}</div>
                        @if(!empty($service->problem_description))
                            <div style="font-size: 11px; color: #64748b; margin-top: 3px;">Problem: {{ $service->problem_description }}</div>
                        @endif
                    </td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #0f172a; text-align: center; border-bottom: 1px solid #e2e8f0;">{{ $item->qty ?? 1 }}</td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #0f172a; text-align: right; border-bottom: 1px solid #e2e8f0;">{{ number_format($item->unit_price ?? 0, 2) }}</td>
                    <td style="padding: 12px 14px; font-size: 12px; color: #0f172a; text-align: right; font-weight: 600; border-bottom: 1px solid #e2e8f0;">{{ number_format($item->total_price ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="padding: 14px; text-align: center; color: #64748b;">No service items found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Bottom Section: Terms (Left) & Totals Card (Right) -->
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 25px;">
        <tr>
            <!-- Terms & Conditions Card (Left) -->
            <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                    <tr>
                        <td style="padding: 14px 18px;">
                            <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #1e293b; margin-bottom: 8px;">TERMS &amp; CONDITIONS</div>
                            <div style="font-size: 11px; color: #475569; line-height: 1.6;">
                                1. Products can be returned within 7 days in their original, unopened condition.<br>
                                2. Refunds or exchanges are offered, but perishable goods cannot be returned.<br>
                                3. Contact us at <strong>01904400205</strong> with a valid receipt for assistance.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            <!-- Invoice Totals Card (Right) -->
            <td style="width: 45%; vertical-align: top;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px;">
                    <tr>
                        <td style="padding: 14px 18px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="font-size: 12px; color: #475569; padding: 4px 0;">Sub Total:</td>
                                    <td style="font-size: 12px; color: #0f172a; font-weight: 600; text-align: right; padding: 4px 0;">{{ number_format($service->bill ?? 0, 2) }} Tk</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px; color: #475569; padding: 4px 0;">Discount:</td>
                                    <td style="font-size: 12px; color: #ef4444; font-weight: 600; text-align: right; padding: 4px 0;">-{{ number_format($service->discount ?? 0, 2) }} Tk</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="border-top: 1px dashed #cbd5e1; padding-top: 6px; margin-top: 4px;"></td>
                                </tr>
                                <tr>
                                    <td style="font-size: 13px; font-weight: 700; color: #0f172a; padding: 4px 0;">Total Payable:</td>
                                    <td style="font-size: 14px; font-weight: 800; color: #4f46e5; text-align: right; padding: 4px 0;">{{ number_format(max(0, ($service->bill ?? 0) - ($service->discount ?? 0)), 2) }} Tk</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px; color: #475569; padding: 4px 0;">Received Amount:</td>
                                    <td style="font-size: 12px; color: #16a34a; font-weight: 700; text-align: right; padding: 4px 0;">{{ number_format($service->paid_amount ?? 0, 2) }} Tk</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 12px; font-weight: 700; color: #dc2626; padding: 4px 0;">Total Due:</td>
                                    <td style="font-size: 13px; font-weight: 800; color: #dc2626; text-align: right; padding: 4px 0;">{{ number_format($service->due_amount ?? 0, 2) }} Tk</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Amount in Words -->
    @php
        $payableTotal = max(0, ($service->bill ?? 0) - ($service->discount ?? 0));
    @endphp
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 10px; margin-bottom: 40px;">
        <tr>
            <td style="padding: 10px 16px; font-size: 12px; color: #334155;">
                <strong style="color: #0f172a; text-transform: uppercase; font-size: 11px;">In Words:</strong>
                <span style="font-style: italic; font-weight: 600; color: #4f46e5; margin-left: 6px;">
                    {{ function_exists('numberToWords') ? numberToWords($payableTotal) : $payableTotal }} Taka Only
                </span>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 50px;">
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
