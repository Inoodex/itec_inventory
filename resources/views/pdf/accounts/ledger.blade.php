<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>General Ledger - {{ $selectedAccount->account_name }}</title>
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
        .info-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 14px;
            color: #475569;
            font-size: 11px;
            margin-bottom: 16px;
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
            padding: 8px 8px;
            text-align: left;
        }
        .items-table td {
            padding: 7px 8px;
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
                <div class="title">General Ledger Report</div>
                <div style="color: #64748b; font-size: 11px; margin-top: 3px;">
                    Account: <strong>[{{ $selectedAccount->account_code }}] {{ $selectedAccount->account_name }}</strong>
                </div>
            </td>
            <td class="text-right">
                <div style="font-size: 11px; font-weight: 700; color: #0f172a;">Period: {{ \Carbon\Carbon::parse($fromDate)->format('d M, Y') }} — {{ \Carbon\Carbon::parse($toDate)->format('d M, Y') }}</div>
                <div style="color: #64748b; font-size: 10px;">Type: {{ strtoupper($selectedAccount->account_type) }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 13%;">Date</th>
                <th style="width: 17%;">Voucher #</th>
                <th style="width: 34%;">Narration</th>
                <th style="width: 12%;" class="text-right">Debit ($)</th>
                <th style="width: 12%;" class="text-right">Credit ($)</th>
                <th style="width: 12%;" class="text-right">Balance ($)</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background-color: #f1f5f9; font-weight: 700;">
                <td>{{ \Carbon\Carbon::parse($fromDate)->format('d M, Y') }}</td>
                <td>-</td>
                <td>Opening Balance Brought Forward</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">{{ number_format($openingBalance, 2) }}</td>
            </tr>

            @php
                $currBal = $openingBalance;
                $totDebit = 0;
                $totCredit = 0;
            @endphp

            @foreach($ledgerItems as $item)
                @php
                    $jv = $item->journalEntry;
                    $d = (float) $item->debit;
                    $c = (float) $item->credit;
                    $totDebit += $d;
                    $totCredit += $c;

                    if ($selectedAccount->isDebitNormal()) {
                        $currBal += ($d - $c);
                    } else {
                        $currBal += ($c - $d);
                    }
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($jv->entry_date)->format('d M, Y') }}</td>
                    <td class="font-weight-bold">{{ $jv->journal_no }}</td>
                    <td>{{ $item->description ?? $jv->description ?? '-' }}</td>
                    <td class="text-right">{{ $d > 0 ? number_format($d, 2) : '-' }}</td>
                    <td class="text-right">{{ $c > 0 ? number_format($c, 2) : '-' }}</td>
                    <td class="text-right font-weight-bold">{{ number_format($currBal, 2) }}</td>
                </tr>
            @endforeach

            <tr style="background-color: #f1f5f9; font-weight: 800;">
                <td colspan="3" class="text-right" style="text-transform: uppercase;">Total Period Activity / Ending Balance:</td>
                <td class="text-right">{{ number_format($totDebit, 2) }}</td>
                <td class="text-right">{{ number_format($totCredit, 2) }}</td>
                <td class="text-right">{{ number_format($currBal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="signature-section">
        <tr>
            <td style="width: 50%;">
                <div class="sig-line">Audited By</div>
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
