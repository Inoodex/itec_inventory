<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Vendor Outstanding Due Report</title>
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
            font-size: 11.5px;
            color: #0f172a;
            line-height: 1.4;
        }

        .header-table {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .report-title {
            text-align: right;
        }

        .report-title h1 {
            font-size: 20px;
            font-weight: 800;
            color: #dc2626;
            margin-bottom: 4px;
        }

        .report-title p {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 18px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px;
        }

        .summary-table td {
            font-size: 11.5px;
            color: #334155;
            padding: 4px 8px;
        }

        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
        }

        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            padding: 8px 10px;
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 8px 10px;
            font-size: 11px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .text-danger { color: #dc2626; }
        .text-success { color: #16a34a; }
    </style>
</head>
<body>
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%;"></td>
            <td style="width:50%;" class="report-title">
                <h1>VENDOR DUE REPORT</h1>
                <p>Generated on {{ date('d M Y, h:i A') }}</p>
                @if($selectedVendor)
                    <p style="color: #0f172a; font-weight: bold;">Filtered for: {{ $selectedVendor->name }}</p>
                @endif
            </td>
        </tr>
    </table>

    <!-- Summary Stats -->
    <table class="summary-table" cellpadding="0" cellspacing="0">
        <tr>
            <td width="33%"><strong>Total Pending Bills:</strong> {{ count($purchases) }}</td>
            <td width="33%"><strong>Total Procured:</strong> ৳{{ number_format($totalBillAmount, 2) }}</td>
            <td width="34%" class="text-right"><strong class="text-danger">Total Outstanding Due:</strong> <span class="text-danger fw-bold">৳{{ number_format($totalDueAmount, 2) }}</span></td>
        </tr>
    </table>

    <!-- Main Data Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 12%; text-align: left;">#Bill No</th>
                <th style="width: 25%; text-align: left;">Vendor Name</th>
                <th style="width: 23%; text-align: left;">Product</th>
                <th style="width: 12%; text-align: center;">Date</th>
                <th style="width: 14%; text-align: right;">Total Bill</th>
                <th style="width: 14%; text-align: right;">Due Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $purchase)
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td class="fw-bold">#PUR-{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <strong>{{ $purchase->vendor->name ?? 'N/A' }}</strong><br>
                        <small style="color: #64748b;">{{ $purchase->vendor->phone ?? '' }}</small>
                    </td>
                    <td>
                        {{ $purchase->product->name ?? 'N/A' }}
                        <small style="color: #64748b;">(Qty: {{ $purchase->quantity }})</small>
                    </td>
                    <td class="text-center">{{ $purchase->created_at ? $purchase->created_at->format('d/m/Y') : 'N/A' }}</td>
                    <td class="text-right">৳{{ number_format($purchase->total_price, 2) }}</td>
                    <td class="text-right fw-bold text-danger">৳{{ number_format($purchase->due, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #64748b;">No outstanding vendor dues found.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($purchases) > 0)
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="4" class="text-right" style="padding: 10px;">GRAND TOTAL:</td>
                <td class="text-right" style="padding: 10px;">৳{{ number_format($totalBillAmount, 2) }}</td>
                <td class="text-right text-danger" style="padding: 10px; font-size: 13px;">৳{{ number_format($totalDueAmount, 2) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Signature Block -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 40px;">
        <tr>
            <td width="100%" align="right" style="vertical-align: bottom;">
                <table align="right" style="width: 180px; margin: 0 0 8px auto; border-collapse: collapse;">
                    <tr>
                        <td style="border-top: 1.5px solid #475569; height: 1px; font-size: 1px; line-height: 1px;">&nbsp;</td>
                    </tr>
                </table>
                <div style="font-size: 11px; font-weight: 600; color: #475569; padding-right: 35px;">Authorized Signature</div>
            </td>
        </tr>
    </table>
</body>
</html>
