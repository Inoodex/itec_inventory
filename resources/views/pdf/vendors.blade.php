<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Vendor Directory Report</title>
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

        .summary-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 12px;
            color: #334155;
            margin-bottom: 20px;
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
                <h1>VENDOR DIRECTORY</h1>
                <p>Generated: {{ date('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <!-- Summary Card -->
    <div class="summary-card">
        <strong>Total Vendors:</strong> {{ count($vendors) }}
    </div>

    <!-- Main Data Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 25%; text-align: left;">Vendor Name</th>
                <th style="width: 18%; text-align: left;">Phone</th>
                <th style="width: 25%; text-align: left;">Email</th>
                <th style="width: 27%; text-align: left;">Address</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vendors as $index => $vendor)
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $vendor->name }}</td>
                    <td>{{ $vendor->phone ?? 'N/A' }}</td>
                    <td>{{ $vendor->email ?? 'N/A' }}</td>
                    <td>{{ $vendor->address ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #64748b;">No vendor records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

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
