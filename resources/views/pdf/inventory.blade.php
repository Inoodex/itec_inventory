<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Inventory Stock Report</title>
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

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background-color: #dcfce7; color: #15803d; }
        .badge-warning { background-color: #fef3c7; color: #b45309; }
        .badge-danger { background-color: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50%;"></td>
            <td style="width:50%;" class="report-title">
                <h1>INVENTORY STOCK REPORT</h1>
                <p>Generated: {{ date('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <!-- Summary Card -->
    <div class="summary-card">
        <strong>Total Items:</strong> {{ count($inventories) }} &nbsp;|&nbsp;
        <strong>In Stock:</strong> <span style="color: #15803d; font-weight: bold;">{{ $inventories->where('current_stock', '>', 5)->count() }}</span> &nbsp;|&nbsp;
        <strong>Low Stock:</strong> <span style="color: #b45309; font-weight: bold;">{{ $inventories->where('current_stock', '<=', 5)->where('current_stock', '>', 0)->count() }}</span> &nbsp;|&nbsp;
        <strong>Out of Stock:</strong> <span style="color: #b91c1c; font-weight: bold;">{{ $inventories->where('current_stock', '<=', 0)->count() }}</span>
    </div>

    <!-- Main Data Table -->
    <table class="items-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">#</th>
                <th style="width: 35%; text-align: left;">Product Name &amp; Model</th>
                <th style="width: 15%; text-align: left;">Brand</th>
                <th style="width: 15%; text-align: center;">Opening Stock</th>
                <th style="width: 15%; text-align: center;">Current Stock</th>
                <th style="width: 15%; text-align: center;">Stock Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $index => $inv)
                @php
                    $prodName = $inv->product->name ?? 'N/A';
                    $prodModel = $inv->product->model ?? '';
                    $brandName = $inv->product->brand->name ?? 'N/A';
                    $stock = $inv->current_stock ?? 0;
                @endphp
                <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="fw-bold">
                        {{ $prodName }}
                        @if($prodModel)
                            <div style="font-weight: normal; font-size: 10px; color: #64748b; margin-top: 2px;">Model: {{ $prodModel }}</div>
                        @endif
                    </td>
                    <td>{{ $brandName }}</td>
                    <td class="text-center">{{ number_format($inv->opening_stock ?? 0) }}</td>
                    <td class="text-center fw-bold">{{ number_format($stock) }}</td>
                    <td class="text-center">
                        @if($stock > 5)
                            <span class="badge badge-success">In Stock</span>
                        @elseif($stock > 0)
                            <span class="badge badge-warning">Low Stock</span>
                        @else
                            <span class="badge badge-danger">Out of Stock</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #64748b;">No inventory items found.</td>
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
