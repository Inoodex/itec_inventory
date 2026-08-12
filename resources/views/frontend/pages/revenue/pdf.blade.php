<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Revenue Report - {{ $revenue->month_name }} {{ $revenue->year }}</title>
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
            padding: 14px 18px;
            font-size: 12px;
            color: #334155;
            margin-bottom: 40px;
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
                <h1>REVENUE REPORT</h1>
                <p>Period: {{ $revenue->month_name }} {{ $revenue->year }}</p>
            </td>
        </tr>
    </table>

    <div class="filter-info">
        <strong>Statement Period:</strong> {{ $revenue->month_name }} {{ $revenue->year }} &nbsp;|&nbsp;
        <strong>Generated On:</strong> {{ now()->format('d M Y') }}
    </div>

    <!-- Main Data Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 20%; text-align: left;">Year</th>
                <th style="width: 20%; text-align: left;">Month</th>
                <th style="width: 20%; text-align: right;">Total Sales</th>
                <th style="width: 20%; text-align: right;">Total Purchases</th>
                <th style="width: 20%; text-align: right;">Total Expenses</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $revenue->year }}</td>
                <td class="fw-bold">{{ $revenue->month_name }}</td>
                <td class="text-right">{{ number_format($revenue->total_sales, 2) }}</td>
                <td class="text-right">{{ number_format($revenue->total_purchases, 2) }}</td>
                <td class="text-right">{{ number_format($revenue->total_expenses, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Summary Box -->
    <div class="summary-card">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 4px 0;"><strong>Total Sales:</strong></td>
                <td style="text-align: right; color: #4f46e5; font-weight: bold; padding: 4px 0;">{{ number_format($revenue->total_sales, 2) }} Tk</td>
            </tr>
            <tr>
                <td style="padding: 4px 0;"><strong>Total Purchases:</strong></td>
                <td style="text-align: right; color: #b45309; font-weight: bold; padding: 4px 0;">{{ number_format($revenue->total_purchases, 2) }} Tk</td>
            </tr>
            <tr>
                <td style="padding: 4px 0;"><strong>Total Expenses:</strong></td>
                <td style="text-align: right; color: #b45309; font-weight: bold; padding: 4px 0;">{{ number_format($revenue->total_expenses, 2) }} Tk</td>
            </tr>
            <tr>
                <td colspan="2" style="border-top: 1px dashed #cbd5e1; padding-top: 8px; margin-top: 8px;"></td>
            </tr>
            <tr>
                <td style="padding: 4px 0; font-size: 13px;"><strong>Net Result:</strong></td>
                <td style="text-align: right; font-size: 14px; font-weight: bold; padding: 4px 0; color: {{ $revenue->net_profit >= 0 ? '#15803d' : '#b91c1c' }};">
                    {{ $revenue->net_profit >= 0 ? 'Profit' : 'Loss' }}: {{ number_format(abs($revenue->net_profit), 2) }} Tk
                </td>
            </tr>
        </table>
    </div>

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
