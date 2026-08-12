<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Service List Report</title>
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
            padding: 10px 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 9px 10px;
            font-size: 11px;
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
                <h1>SERVICE LIST REPORT</h1>
                <p>Generated: {{ date('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <div class="filter-info">
        @if ($request->from && $request->to)
            <strong>Date Range:</strong> {{ $request->from }} to {{ $request->to }} &nbsp;|&nbsp;
        @endif
        @if ($request->service_type)
            <strong>Payment Status:</strong> {{ ucfirst($request->service_type) }} &nbsp;|&nbsp;
        @endif
        @if ($request->serach_by && $request->key)
            <strong>Search ({{ ucfirst($request->serach_by) }}):</strong> {{ $request->key }} &nbsp;|&nbsp;
        @endif
        <strong>Total Records:</strong> {{ count($services) }}
    </div>

    <!-- Main Data Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 12%; text-align: left;">Date</th>
                <th style="width: 24%; text-align: left;">Customer</th>
                <th style="width: 15%; text-align: left;">Phone</th>
                <th style="width: 11%; text-align: right;">Bill (Tk)</th>
                <th style="width: 11%; text-align: right;">Paid (Tk)</th>
                <th style="width: 11%; text-align: right;">Due (Tk)</th>
                <th style="width: 11%; text-align: left;">Repaired By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $index => $service)
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $service->status == '0' ? ($service->created_at ? $service->created_at->format('d-m-Y') : 'N/A') : $service->complated_date }}</td>
                    <td class="fw-bold">{{ $service->name }}</td>
                    <td>{{ $service->phone ?? 'N/A' }}</td>
                    <td class="text-right fw-bold">{{ number_format($service->bill ?? 0, 2) }}</td>
                    <td class="text-right" style="color: #16a34a;">{{ number_format($service->paid_amount ?? 0, 2) }}</td>
                    <td class="text-right" style="color: {{ ($service->due_amount ?? 0) > 0 ? '#dc2626' : '#16a34a' }}; font-weight: bold;">
                        {{ number_format($service->due_amount ?? 0, 2) }}
                    </td>
                    <td>{{ $service->repaired_by ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">No service records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Box -->
    <div class="summary-card">
        <strong style="margin-right: 15px;">Total Services: {{ count($services) }}</strong> &nbsp;|&nbsp;
        <strong style="color: #4f46e5; margin-right: 15px;">Total Billing: {{ number_format($services->sum('bill'), 2) }} Tk</strong> &nbsp;|&nbsp;
        <strong style="color: #16a34a; margin-right: 15px;">Total Paid: {{ number_format($services->sum('paid_amount'), 2) }} Tk</strong> &nbsp;|&nbsp;
        <strong style="color: #dc2626;">Total Dues: {{ number_format($services->sum('due_amount'), 2) }} Tk</strong>
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
