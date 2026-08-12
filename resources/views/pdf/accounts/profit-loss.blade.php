<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Income Statement (P&L)</title>
    <style>
        @page {
            @if(!empty($padBase64))
            background-image: url('{{ $padBase64 }}');
            background-image-resize: 6;
            @endif
            margin-top: 45mm;
            margin-bottom: 25mm;
            margin-left: 15mm;
            margin-right: 15mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .title {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
        }
        .summary-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 18px;
        }
        .items-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 16px;
        }
        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 10px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: 700; }
        .signature-section {
            width: 100%;
            margin-top: 35px;
        }
        .sig-line {
            width: 180px;
            border-top: 1.5px solid #475569;
            text-align: center;
            padding-top: 5px;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td>
                <div class="title">Profit & Loss Statement</div>
                <div style="color: #64748b; font-size: 11px; margin-top: 3px;">Income Statement for Specified Period</div>
            </td>
            <td class="text-right">
                <div style="font-size: 11px; font-weight: 700; color: #0f172a;">Period: {{ \Carbon\Carbon::parse($fromDate)->format('d M, Y') }} — {{ \Carbon\Carbon::parse($toDate)->format('d M, Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- Operating Revenues -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 75%;">Operating Revenues</th>
                <th style="width: 25%;" class="text-right">Amount ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueData as $rev)
                <tr>
                    <td>{{ $rev['account']->account_name }} ({{ $rev['account']->account_code }})</td>
                    <td class="text-right font-weight-bold">{{ number_format($rev['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f1f5f9; font-weight: 800;">
                <td>TOTAL OPERATING REVENUE:</td>
                <td class="text-right">{{ number_format($totalRevenue, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Operating Expenses -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 75%;">Operating Expenses</th>
                <th style="width: 25%;" class="text-right">Amount ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenseData as $exp)
                <tr>
                    <td>{{ $exp['account']->account_name }} ({{ $exp['account']->account_code }})</td>
                    <td class="text-right font-weight-bold">{{ number_format($exp['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f1f5f9; font-weight: 800;">
                <td>TOTAL OPERATING EXPENSES:</td>
                <td class="text-right">{{ number_format($totalExpense, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Net Profit Card -->
    <div class="summary-card" style="text-align: center;">
        <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700;">Net Operating Income / (Loss) for Period</div>
        <div style="font-size: 18px; font-weight: 800; color: {{ $netProfit >= 0 ? '#15803d' : '#b91c1c' }}; margin-top: 4px;">
            {{ $netProfit >= 0 ? '$' : '-$' }}{{ number_format(abs($netProfit), 2) }}
        </div>
    </div>

    <table class="signature-section">
        <tr>
            <td style="width: 50%;">
                <div class="sig-line">Prepared By</div>
            </td>
            <td style="width: 50%;" class="text-right">
                <table align="right">
                    <tr>
                        <td class="sig-line">Authorized Signature</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
