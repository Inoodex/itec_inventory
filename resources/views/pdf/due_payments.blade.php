<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Due Payments Report</title>
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
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .report-title p {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }

        .summary-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: #334155;
            margin-bottom: 20px;
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
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 10px 12px;
            font-size: 12px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-retail { background-color: #e0e7ff; color: #3730a3; }
        .badge-project { background-color: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%;"></td>
            <td style="width:50%;" class="report-title">
                <h1>DUE PAYMENTS REPORT</h1>
                <p>Generated: {{ date('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <!-- Summary Card -->
    <div class="summary-card">
        <strong>Total Outstanding Orders:</strong> {{ count($sales) }} &nbsp;|&nbsp;
        <strong>Total Payable:</strong> <span style="color: #4f46e5; font-weight: bold;">{{ number_format($sales->sum('payble'), 2) }}</span> &nbsp;|&nbsp;
        <strong>Total Paid:</strong> <span style="color: #16a34a; font-weight: bold;">{{ number_format($sales->sum('advanced_payment'), 2) }}</span> &nbsp;|&nbsp;
        <strong>Total Dues Outstanding:</strong> <span style="color: #dc2626; font-weight: bold;">{{ number_format($sales->sum('due_payment'), 2) }}</span>
    </div>

    <!-- Main Data Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 12%; text-align: left;">Date</th>
                <th style="width: 16%; text-align: left;">Order No</th>
                <th style="width: 12%; text-align: center;">Type</th>
                <th style="width: 25%; text-align: left;">Customer / Client</th>
                <th style="width: 10%; text-align: right;">Payable</th>
                <th style="width: 10%; text-align: right;">Paid</th>
                <th style="width: 10%; text-align: right;">Due</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $index => $item)
                @php
                    $customerName = $item->sale_type == 'project' 
                        ? ($item->client->name ?? 'N/A') 
                        : ($item->customer->name ?? 'N/A');
                @endphp
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->created_at ? $item->created_at->format('d-m-Y') : 'N/A' }}</td>
                    <td class="fw-bold">{{ $item->order_no }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->sale_type == 'project' ? 'badge-project' : 'badge-retail' }}">
                            {{ ucfirst($item->sale_type) }}
                        </span>
                    </td>
                    <td class="fw-bold">{{ $customerName }}</td>
                    <td class="text-right">{{ number_format($item->payble ?? 0, 2) }}</td>
                    <td class="text-right" style="color: #16a34a;">{{ number_format($item->advanced_payment ?? 0, 2) }}</td>
                    <td class="text-right fw-bold" style="color: #dc2626;">{{ number_format($item->due_payment ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">No due payment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block -->
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
