<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Purchase Report</title>
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

        .header-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 12px;
        }

        .report-title {
            text-align: right;
        }

        .report-title h1 {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .report-title p {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        .filter-info {
            font-size: 11px;
            color: #475569;
            margin-bottom: 20px;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
        }

        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 25px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
        }

        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 10px 14px;
            font-size: 12px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: #334155;
            margin-bottom: 40px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%;"></td>
            <td style="width:50%;" class="report-title">
                <h1>PURCHASE REPORT</h1>
                <p>Generated: {{ now()->format('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <div class="filter-info">
        <strong>Date Range:</strong> {{ $filters['from'] }} to {{ $filters['to'] }} &nbsp;|&nbsp;
        <strong>Product:</strong> {{ $filters['product'] }} &nbsp;|&nbsp;
        <strong>Vendor:</strong> {{ $filters['vendor'] }} &nbsp;|&nbsp;
        <strong>Total Records:</strong> {{ $purchases->count() }}
    </div>

    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">#</th>
                <th style="width: 50%; text-align: left;">Product Name</th>
                <th style="width: 20%; text-align: center;">Total Quantity</th>
                <th style="width: 24%; text-align: right;">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $index => $purchase)
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $purchase->product->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $purchase->total_qty }}</td>
                    <td class="text-right">{{ number_format($purchase->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No data available</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-card">
        <strong style="color: #4f46e5; margin-right: 20px;">Total Quantity: {{ $purchases->sum('total_qty') }}</strong>
        <strong style="color: #0f172a;">Grand Total: {{ number_format($purchases->sum('total_amount'), 2) }}</strong>
    </div>

    <!-- Signatures -->
    <table style="width: 100%; border-collapse: collapse; margin-top: 50px;">
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
