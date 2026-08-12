<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Quotations Report</title>
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

        .summary-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: #334155;
            margin-bottom: 30px;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%;"></td>
            <td style="width:50%;" class="report-title">
                <h1>QUOTATIONS REPORT</h1>
                <p>Generated: {{ date('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <div class="filter-info">
        @if ($request->date_from)
            <strong>From:</strong> {{ date('d M Y', strtotime($request->date_from)) }} &nbsp;|&nbsp;
        @endif
        @if ($request->date_to)
            <strong>To:</strong> {{ date('d M Y', strtotime($request->date_to)) }} &nbsp;|&nbsp;
        @endif
        <strong>Total Quotations:</strong> {{ $quotations->count() }}
    </div>

    <!-- Main Data Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 22%; text-align: left;">Quotation No</th>
                <th style="width: 30%; text-align: left;">Client</th>
                <th style="width: 15%; text-align: left;">Date</th>
                <th style="width: 15%; text-align: left;">Expiry Date</th>
                <th style="width: 13%; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($quotations as $index => $quotation)
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $quotation->quotation_number }}</td>
                    <td>{{ $quotation->client?->name ?? $quotation->client_name ?? 'N/A' }}</td>
                    <td>{{ $quotation->quotation_date ? $quotation->quotation_date->format('d M Y') : 'N/A' }}</td>
                    <td>{{ $quotation->expiry_date ? $quotation->expiry_date->format('d M Y') : 'N/A' }}</td>
                    <td class="text-right fw-bold" style="color: #4f46e5;">{{ number_format($quotation->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #64748b;">No quotations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Box -->
    <div class="summary-card">
        <strong style="margin-right: 20px;">Total Quotations: {{ $quotations->count() }}</strong> &nbsp;|&nbsp;
        <strong style="color: #4f46e5;">Total Amount: {{ number_format($quotations->sum('total_amount'), 2) }}</strong>
    </div>

    <!-- Signature Block at Bottom -->
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