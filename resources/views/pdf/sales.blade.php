<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales List Report</title>
    <style>
        @page {
            margin: 140px 45px 80px 45px;
            size: A4 portrait;
        }
        .pdf-bg-pad {
            position: fixed;
            top: -140px;
            left: -45px;
            width: 210mm;
            height: 297mm;
            z-index: -1000;
        }
        .pdf-bg-pad img {
            width: 210mm;
            height: 297mm;
        }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin-top: 5px;
        }
        .filter-info {
            font-size: 9px;
            color: #64748b;
            margin-top: 4px;
        }
        table.sales-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.sales-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }
        table.sales-table td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 9px;
        }
        table.sales-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-retail { background-color: #e0e7ff; color: #4338ca; }
        .badge-project { background-color: #fef3c7; color: #b45309; }
        .summary-box {
            margin-top: 15px;
            float: right;
            width: 250px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 8px;
            border-radius: 4px;
        }
        .summary-box table { width: 100%; font-size: 10px; }
        .summary-box td { padding: 3px 0; }
        .footer {
            margin-top: 40px;
            width: 100%;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="pdf-bg-pad">
        <img src="{{ public_path('assets/invoice/final_pad.png') }}" />
    </div>

    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <div class="company-name">Intelligent Technology</div>
                    <div style="font-size: 9px; color: #475569;">Inventory &amp; Sales Management System</div>
                </td>
                <td class="text-end">
                    <div class="report-title">Sales Summary Report</div>
                    <div class="filter-info">Generated on: {{ date('d M Y') }}</div>
                    @if(request('from') && request('to'))
                        <div class="filter-info">Date Range: {{ date('d M Y', strtotime(request('from'))) }} — {{ date('d M Y', strtotime(request('to'))) }}</div>
                    @elseif(request('month'))
                        <div class="filter-info">Month: {{ date('F Y', strtotime(request('month'))) }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="sales-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">#</th>
                <th width="12%">Date</th>
                <th width="15%">Order No</th>
                <th width="26%">Customer / Client Name</th>
                <th width="14%">Phone</th>
                <th width="10%" class="text-center">Sale Type</th>
                <th width="10%">Sales By</th>
                <th width="9%" class="text-end">Payable (৳)</th>
            </tr>
        </thead>
        <tbody>
            @php $totalSalesAmount = 0; @endphp
            @forelse($services as $index => $sale)
                @php
                    $custName = $sale->sale_type == 'project' ? ($sale->client->name ?? 'N/A') : ($sale->customer->name ?? 'N/A');
                    $custPhone = $sale->sale_type == 'project' ? ($sale->client->phone ?? 'N/A') : ($sale->customer->phone ?? 'N/A');
                    $salesPersonName = $sale->salesPerson->name ?? 'N/A';
                    $amount = $sale->payble ?? $sale->bill ?? $sale->total ?? 0;
                    $totalSalesAmount += $amount;
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $sale->created_at ? $sale->created_at->format('d M Y') : 'N/A' }}</td>
                    <td class="fw-bold">{{ $sale->order_no }}</td>
                    <td>{{ $custName }}</td>
                    <td>{{ $custPhone }}</td>
                    <td class="text-center">
                        <span class="badge {{ $sale->sale_type == 'project' ? 'badge-project' : 'badge-retail' }}">
                            {{ ucfirst($sale->sale_type) }}
                        </span>
                    </td>
                    <td>{{ $salesPersonName }}</td>
                    <td class="text-end fw-bold">{{ number_format($amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">No sales records found for the selected filter criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <table>
            <tr>
                <td><strong>Total Sales Records:</strong></td>
                <td class="text-end fw-bold">{{ count($services) }}</td>
            </tr>
            <tr>
                <td><strong>Total Sales Revenue:</strong></td>
                <td class="text-end fw-bold" style="color: #4f46e5; font-size: 11px;">{{ number_format($totalSalesAmount, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        Intelligent Technology Inventory System &bull; Confidential Sales List Report
    </div>
</body>
</html>
