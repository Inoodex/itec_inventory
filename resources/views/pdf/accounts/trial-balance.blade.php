<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trial Balance - {{ $asOfDate }}</title>
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
            margin-bottom: 20px;
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
                <div class="title">Trial Balance Report</div>
                <div style="color: #64748b; font-size: 11px; margin-top: 3px;">Statement of Account Equilibrium</div>
            </td>
            <td class="text-right">
                <div style="font-size: 11px; font-weight: 700; color: #0f172a;">As of: {{ \Carbon\Carbon::parse($asOfDate)->format('d F, Y') }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Account Code</th>
                <th style="width: 45%;">Account Title</th>
                <th style="width: 16%;">Account Type</th>
                <th style="width: 12%;" class="text-right">Debit ($)</th>
                <th style="width: 12%;" class="text-right">Credit ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td class="font-weight-bold">{{ $row['account']->account_code }}</td>
                    <td>{{ $row['account']->account_name }}</td>
                    <td style="text-transform: uppercase; color: #64748b;">{{ $row['account']->account_type }}</td>
                    <td class="text-right">{{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '-' }}</td>
                    <td class="text-right">{{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '-' }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f1f5f9; font-weight: 800;">
                <td colspan="3" class="text-right" style="text-transform: uppercase;">Total Trial Balance:</td>
                <td class="text-right">{{ number_format($totalDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totalCredit, 2) }}</td>
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
                        <td class="sig-line">Chief Financial Officer</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
