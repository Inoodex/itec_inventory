<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Balance Sheet - {{ $asOfDate }}</title>
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
                <div class="title">Balance Sheet</div>
                <div style="color: #64748b; font-size: 11px; margin-top: 3px;">Statement of Financial Position</div>
            </td>
            <td class="text-right">
                <div style="font-size: 11px; font-weight: 700; color: #0f172a;">As of: {{ \Carbon\Carbon::parse($asOfDate)->format('d F, Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- Assets -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 75%;">Assets</th>
                <th style="width: 25%;" class="text-right">Amount ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assetData as $ast)
                <tr>
                    <td>{{ $ast['account']->account_name }} ({{ $ast['account']->account_code }})</td>
                    <td class="text-right font-weight-bold">{{ number_format($ast['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f1f5f9; font-weight: 800;">
                <td>TOTAL ASSETS:</td>
                <td class="text-right">{{ number_format($totalAssets, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Liabilities & Equity -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 75%;">Liabilities & Equity</th>
                <th style="width: 25%;" class="text-right">Amount ($)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Liabilities -->
            <tr style="background-color: #f8fafc; font-weight: 700; color: #dc2626;">
                <td colspan="2" style="text-transform: uppercase;">Liabilities</td>
            </tr>
            @foreach($liabilityData as $lia)
                <tr>
                    <td style="padding-left: 20px;">{{ $lia['account']->account_name }} ({{ $lia['account']->account_code }})</td>
                    <td class="text-right font-weight-bold">{{ number_format($lia['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr style="font-weight: 700; color: #64748b;">
                <td>Total Liabilities:</td>
                <td class="text-right">{{ number_format($totalLiabilities, 2) }}</td>
            </tr>

            <!-- Equity -->
            <tr style="background-color: #f8fafc; font-weight: 700; color: #16a34a;">
                <td colspan="2" style="text-transform: uppercase;">Equity</td>
            </tr>
            @foreach($equityData as $eq)
                <tr>
                    <td style="padding-left: 20px;">{{ $eq['account']->account_name }} ({{ $eq['account']->account_code }})</td>
                    <td class="text-right font-weight-bold">{{ number_format($eq['amount'], 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="padding-left: 20px;">Current Period Retained Earnings (Net Profit)</td>
                <td class="text-right font-weight-bold">{{ number_format($currentEarnings, 2) }}</td>
            </tr>
            <tr style="font-weight: 700; color: #64748b;">
                <td>Total Equity:</td>
                <td class="text-right">{{ number_format($totalEquityWithEarnings, 2) }}</td>
            </tr>

            <tr style="background-color: #f1f5f9; font-weight: 800;">
                <td>TOTAL LIABILITIES & EQUITY:</td>
                <td class="text-right">{{ number_format($totalLiabAndEquity, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="signature-section">
        <tr>
            <td style="width: 50%;">
                <div class="sig-line">Prepared By</div>
            </td>
            <td style="width: 50%;" class="text-right">
                <table align="right">
                    <tr>
                        <td class="sig-line">Managing Director</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
