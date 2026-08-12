<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Bills Report</title>
    @php
        $toFileUrl = function ($path) {
            return 'file:///' . str_replace(['\\', ' '], ['/', '%20'], public_path($path));
        };
    @endphp
    <style>
        @page {
            background-image: url('{{ $toFileUrl('assets/invoice/final_pad.png') }}');
            background-image-resize: 6;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            background: url('{{ $toFileUrl('assets/invoice/final_pad.png') }}') no-repeat center top / 100% 100% fixed transparent !important;
        }
        .header-table { width: 100%; margin-bottom: 20px; }
        .header-table td { vertical-align: top; padding: 4px; }
        .report-title { text-align: right; }
        .report-title h1 {
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .report-title p { font-size: 12px; margin-top: 4px; }
        .filter-info {
            font-size: 11px;
            color: #666;
            margin-bottom: 15px;
            padding: 8px 10px;
            background: #fafafa;
            border: 1px solid #ddd;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead tr { background-color: #626262; }
        .items-table th {
            background-color: #626262;
            color: #fff !important;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ccc;
            padding: 7px 10px;
            text-align: left;
            font-size: 11px;
        }
        .items-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .in-words {
            font-size: 12px;
            margin-bottom: 30px;
            padding: 8px 10px;
            border: 1px solid #ddd;
            background: #fafafa;
            text-align: center;
        }
        .signature-table { width: 100%; margin-top: 40px; }
        .signature-table td {
            text-align: center;
            font-size: 11px;
            width: 50%;
            padding: 0 40px;
        }
        hr.signature-line {
            border: none;
            border-top: 1px solid #333;
            margin: 0 0 6px 0;
        }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%;"></td>
            <td style="width:50%;" class="report-title">
                <h1>BILLS REPORT</h1>
                <p>Generated: {{ now()->format('d-m-Y h:i A') }}</p>
            </td>
        </tr>
    </table>

    <div class="filter-info">
        @if ($request->type) Type: {{ $request->type }} | @endif
        @if ($request->date_from) From: {{ $request->date_from }} | @endif
        @if ($request->date_to) To: {{ $request->date_to }} | @endif
        Total: {{ $bills->count() }} bill(s)
    </div>

    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th>SL</th>
                <th>Bill Number</th>
                <th>Type</th>
                <th>Bill Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bills as $bill)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $bill->bill_number }}</td>
                    <td>{{ ucfirst($bill->type) }}</td>
                    <td>{{ $bill->bill_date->format('d-m-Y') }}</td>
                    <td>{{ number_format($bill->total_amount, 2) }} Tk</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;">No bills found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="in-words">
        <strong>Total Bills:</strong> {{ $bills->count() }} |
        <strong>Total Amount:</strong> {{ number_format($bills->sum('total_amount'), 2) }} Tk
    </div>

    {{-- <table class="signature-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <hr class="signature-line">
                Authorized Signature
            </td>
        </tr>
    </table> --}}
</body>
</html>