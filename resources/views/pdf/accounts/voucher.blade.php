<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Journal Voucher - {{ $journalEntry->journal_no }}</title>
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
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
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
            margin-bottom: 18px;
        }
        .items-table {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            padding: 9px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-weight-bold { font-weight: 700; }
        .signature-section {
            width: 100%;
            margin-top: 40px;
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
                <div class="title">Journal Voucher</div>
                <div style="color: #64748b; font-size: 11px; margin-top: 4px;">Double-Entry Transaction Record</div>
            </td>
            <td class="text-right">
                <div style="font-size: 14px; font-weight: 800; color: #0f172a;">{{ $journalEntry->journal_no }}</div>
                <div style="color: #64748b; font-size: 11px;">Date: {{ \Carbon\Carbon::parse($journalEntry->entry_date)->format('d M, Y') }}</div>
            </td>
        </tr>
    </table>

    <div class="info-card">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <strong>Reference Type:</strong> {{ strtoupper($journalEntry->reference_type) }}
                    @if($journalEntry->reference_id) (#{{ $journalEntry->reference_id }}) @endif
                </td>
                <td style="width: 50%;" class="text-right">
                    <strong>Status:</strong> {{ strtoupper($journalEntry->status) }}
                </td>
            </tr>
            @if($journalEntry->description)
            <tr>
                <td colspan="2" style="padding-top: 6px;">
                    <strong>Narration:</strong> {{ $journalEntry->description }}
                </td>
            </tr>
            @endif
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%;">Code</th>
                <th style="width: 35%;">Account Title</th>
                <th style="width: 26%;">Description</th>
                <th style="width: 12%;" class="text-right">Debit ($)</th>
                <th style="width: 12%;" class="text-right">Credit ($)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($journalEntry->items as $item)
                <tr>
                    <td class="font-weight-bold">{{ $item->account->account_code }}</td>
                    <td>{{ $item->account->account_name }}</td>
                    <td style="color: #64748b;">{{ $item->description ?? '-' }}</td>
                    <td class="text-right font-weight-bold">{{ $item->debit > 0 ? number_format($item->debit, 2) : '-' }}</td>
                    <td class="text-right font-weight-bold">{{ $item->credit > 0 ? number_format($item->credit, 2) : '-' }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #f1f5f9; font-weight: 800;">
                <td colspan="3" class="text-right" style="text-transform: uppercase;">Total Voucher Amount:</td>
                <td class="text-right">{{ number_format($journalEntry->total_debit, 2) }}</td>
                <td class="text-right">{{ number_format($journalEntry->total_credit, 2) }}</td>
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
                        <td class="sig-line">Authorized Signature</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
